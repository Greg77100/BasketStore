<?php

namespace App\Controller;

use App\Entity\BillingAdressOrder;
use App\Form\BillingAdressOrderType;
use App\Repository\BillingAdressOrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class BillingAdressOrderController extends AbstractController{
    #[Route('/admin/Facturation/adresse/commande', name: 'app_billing_adress_order_index', methods: ['GET'])]
    public function index(BillingAdressOrderRepository $billingAdressOrderRepository): Response
    {
        return $this->render('billing_adress_order/index.html.twig', [
            'billing_adress_orders' => $billingAdressOrderRepository->findAll(),
        ]);
    }

    #[Route('/Facturation/adresse/commande/creation', name: 'app_billing_adress_order_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $billingAdressOrder = new BillingAdressOrder();
        $form = $this->createForm(BillingAdressOrderType::class, $billingAdressOrder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($billingAdressOrder);
            $entityManager->flush();

            return $this->redirectToRoute('app_billing_adress_order_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('billing_adress_order/new.html.twig', [
            'billing_adress_order' => $billingAdressOrder,
            'form' => $form,
        ]);
    }

    #[Route('/Facturation/adresse/commande/{id}', name: 'app_billing_adress_order_show', methods: ['GET'])]
    public function show(BillingAdressOrder $billingAdressOrder): Response
    {
        return $this->render('billing_adress_order/show.html.twig', [
            'billing_adress_order' => $billingAdressOrder,
        ]);
    }

    #[Route('/Facturation/adresse/commande/modifier/{id}', name: 'app_billing_adress_order_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, BillingAdressOrder $billingAdressOrder, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BillingAdressOrderType::class, $billingAdressOrder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_billing_adress_order_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('billing_adress_order/edit.html.twig', [
            'billing_adress_order' => $billingAdressOrder,
            'form' => $form,
        ]);
    }

    #[Route('/Facturation/adresse/commande/{id}', name: 'app_billing_adress_order_delete', methods: ['POST'])]
    public function delete(Request $request, BillingAdressOrder $billingAdressOrder, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$billingAdressOrder->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($billingAdressOrder);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_billing_adress_order_index', [], Response::HTTP_SEE_OTHER);
    }
}
