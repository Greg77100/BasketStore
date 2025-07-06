<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderType;
use App\Entity\OrderList;
use App\Service\CartService;
use App\Form\Model\AdressData;
use App\Form\CombinedAdressType;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class OrderController extends AbstractController
{
    #[Route('/admin/commande', name: 'app_order_index', methods: ['GET'])]
    public function index(OrderRepository $orderRepository): Response
    {
        return $this->render('order/index.html.twig', [
            'orders' => $orderRepository->findAll(),
        ]);
    }

    #[Route('/commande/creation', name: 'app_order_new')]
    public function new(Request $request, CartService $cartService, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            $request->getSession()->set('cart_backup', $request->getSession()->get('cart', []));
            return $this->redirectToRoute('app_login');
        }
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $order = new Order();

        $cart = $cartService->getCart();
        $order->setTotalOrder($cartService->getTotal());
        $order->setDateOrder(new \DateTimeImmutable());
        $order->setStatutOrder('en attente');
        $order->setUser($user);

        // ✅ Créer le modèle de données combinées
        $adressData = new AdressData();

        // ✅ Préremplissage depuis les adresses du compte utilisateur
        if ($user) {
            // Préremplir la livraison si dispo
            $shippingAdresses = $user->getShippingAdresses();
            if (!$shippingAdresses->isEmpty()) {
                $defaultShipping = $shippingAdresses->first();

                $shippingOrder = new \App\Entity\ShippingAdressOrder();
                $shippingOrder
                    ->setFullName($defaultShipping->getFullName())
                    ->setStreetAdress($defaultShipping->getStreetAdress())
                    ->setCity($defaultShipping->getCity())
                    ->setPostalCode($defaultShipping->getPostalCode())
                    ->setPhone($defaultShipping->getPhone());

                $adressData->shipping = $shippingOrder;
            }

            // Préremplir la facturation si dispo
            $billing = $user->getBillingAdress();
            if ($billing) {
                $billingOrder = new \App\Entity\BillingAdressOrder();
                $billingOrder
                    ->setFullName($billing->getFullName())
                    ->setStreetAdress($billing->getStreetAdress())
                    ->setCity($billing->getCity())
                    ->setPostalCode($billing->getPostalCode());
                    

                $adressData->billing = $billingOrder;
            }
        }

        // ✅ Créer le formulaire prérempli
        $form = $this->createForm(CombinedAdressType::class, $adressData);
        $form->handleRequest($request);

        // ✅ Si la case "même adresse" est cochée, dupliquer la shipping vers billing
        if ($form->isSubmitted() && $adressData->sameAdress) {
            $shipping = $adressData->shipping;

            $billing = new \App\Entity\BillingAdressOrder();
            $billing
                ->setFullName($shipping->getFullName())
                ->setStreetAdress($shipping->getStreetAdress())
                ->setCity($shipping->getCity())
                ->setPostalCode($shipping->getPostalCode())
                ->setCreatedAt($shipping->getCreatedAt());

            $adressData->billing = $billing;
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $billingAdressOrder = $adressData->billing;
            $adressData->shipping->setCreatedAt(new \DateTimeImmutable());
            $billingAdressOrder->setCreatedAt(new \DateTimeImmutable());

            // Lier les adresses à la commande
            $adressData->shipping->setOrder($order);
            $billingAdressOrder->setOrder($order);
            $order->setShippingAdressOrder($adressData->shipping);
            $order->setBillingAdressOrder($billingAdressOrder);

            // Générer les lignes de commande
            foreach ($cart as $item) {
                $orderList = new OrderList();
                $orderList->setProduct($item['product']);
                $orderList->setQuantity($item['quantity']);
                $orderList->setPrice($item['product']->getPrice());

                $order->addOrderList($orderList);
                $orderList->setOrders($order);
            }

            // Persister en base
            $entityManager->persist($adressData->shipping);
            $entityManager->persist($adressData->billing);
            $entityManager->persist($order);
            $entityManager->flush();

            // Redirection vers le paiement
            return $this->redirectToRoute('app_payment_new', [
                'orderId' => $order->getId()
            ]);
        }

        return $this->render('order/adress.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/commande/voir/{id}', name: 'app_order_show', methods: ['GET'])]
    public function show(Order $order): Response
    {
        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/commande/modifier/{id}', name: 'app_order_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Order $order, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_order_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('order/edit.html.twig', [
            'order' => $order,
            'form' => $form,
        ]);
    }

    #[Route('/commande/supprimer/{id}', name: 'app_order_delete', methods: ['POST'])]
    public function delete(Request $request, Order $order, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$order->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($order);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_order_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/commande/confirmation/{id}', name: 'app_order_confirmation')]
    public function confirmation(Order $order): Response
    {
        return $this->render('order/confirmation.html.twig', [
            'order' => $order,
        ]);
    }
}
