<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\ShippingAdressOrder;
use App\Form\ShippingAdressOrderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ShippingAdressOrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


final class ShippingAdressOrderController extends AbstractController{
    #[Route('/admin/livraison/commande', name: 'app_shipping_adress_order_index', methods: ['GET'])]
    public function index(ShippingAdressOrderRepository $shippingAdressOrderRepository): Response
    {
        return $this->render('shipping_adress_order/index.html.twig', [
            'shipping_adress_orders' => $shippingAdressOrderRepository->findAll(),
        ]);
    }

    #[Route('/livraison/commande/{orderId}', name: 'app_shipping_adress_order_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, int $orderId): Response
    {
        $shippingAdressOrder = new ShippingAdressOrder();
        $form = $this->createForm(ShippingAdressOrderType::class, $shippingAdressOrder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $order = $entityManager->getRepository(Order::class)->find($orderId);
            $shippingAdressOrder->setCreatedAt(new \DateTimeImmutable());
            $shippingAdressOrder->setOrder($order);
            $order->setShippingAdressOrder($shippingAdressOrder);
            $entityManager->persist($shippingAdressOrder);
            $entityManager->persist($order);
            $entityManager->flush();

            //return $this->redirectToRoute('app_payment_new', [], Response::HTTP_SEE_OTHER);
            return $this->redirectToRoute('app_payment_new', [
                'orderId' => $order->getId()
            ]);
            
        }

        return $this->render('shipping_adress_order/new.html.twig', [
            'shipping_adress_order' => $shippingAdressOrder,
            'form' => $form,
        ]);
    }

    #[Route('/livraison/commande/{id}', name: 'app_shipping_adress_order_show', methods: ['GET'])]
    public function show(ShippingAdressOrder $shippingAdressOrder): Response
    {
        return $this->render('shipping_adress_order/show.html.twig', [
            'shipping_adress_order' => $shippingAdressOrder,
        ]);
    }

    #[Route('/livraison/commande/modifier/{id}', name: 'app_shipping_adress_order_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ShippingAdressOrder $shippingAdressOrder, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ShippingAdressOrderType::class, $shippingAdressOrder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_shipping_adress_order_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('shipping_adress_order/edit.html.twig', [
            'shipping_adress_order' => $shippingAdressOrder,
            'form' => $form,
        ]);
    }

    #[Route('/livraison/commande/supprimer/{id}', name: 'app_shipping_adress_order_delete', methods: ['POST'])]
    public function delete(Request $request, ShippingAdressOrder $shippingAdressOrder, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$shippingAdressOrder->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($shippingAdressOrder);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_shipping_adress_order_index', [], Response::HTTP_SEE_OTHER);
    }
}
