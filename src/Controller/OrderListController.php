<?php

namespace App\Controller;

use App\Entity\OrderList;
use App\Form\OrderListType;
use App\Repository\OrderListRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/order/list')]
final class OrderListController extends AbstractController{
    #[Route(name: 'app_order_list_index', methods: ['GET'])]
    public function index(OrderListRepository $orderListRepository): Response
    {
        return $this->render('order_list/index.html.twig', [
            'order_lists' => $orderListRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_order_list_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $orderList = new OrderList();
        $form = $this->createForm(OrderListType::class, $orderList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($orderList);
            $entityManager->flush();

            return $this->redirectToRoute('app_order_list_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('order_list/new.html.twig', [
            'order_list' => $orderList,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_order_list_show', methods: ['GET'])]
    public function show(OrderList $orderList): Response
    {
        return $this->render('order_list/show.html.twig', [
            'order_list' => $orderList,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_order_list_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, OrderList $orderList, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OrderListType::class, $orderList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_order_list_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('order_list/edit.html.twig', [
            'order_list' => $orderList,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_order_list_delete', methods: ['POST'])]
    public function delete(Request $request, OrderList $orderList, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$orderList->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($orderList);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_order_list_index', [], Response::HTTP_SEE_OTHER);
    }
}
