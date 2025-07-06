<?php

namespace App\Controller;

use App\Entity\BillingAdress;
use App\Form\BillingAdressType;
use App\Repository\BillingAdressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class BillingAdressController extends AbstractController{
    #[Route('/admin/facturation/adresse', name: 'app_billing_adress_index', methods: ['GET'])]
    public function index(BillingAdressRepository $billingAdressRepository): Response
    {
        return $this->render('billing_adress/index.html.twig', [
            'billing_adress' => $billingAdressRepository->findAll(),
        ]);
    }

    #[Route('/facturation/adresse/creation', name: 'app_billing_adress_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser(); // Récupère l'utilisateur connecté
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour ajouter une adresse.');
        }

        $billingAdress = new BillingAdress();
        $billingAdress->setUser($user); // Lier l'adresse à l'utilisateur connecté
        $form = $this->createForm(BillingAdressType::class, $billingAdress);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($billingAdress);
            $entityManager->flush();

            $this->addFlash('success', 'Votre adresse de facturation a été ajoutée.');

            return $this->redirectToRoute('app_account', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('billing_adress/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/facturation/adresse/{id}', name: 'app_billing_adress_show', methods: ['GET'])]
    public function show(BillingAdress $billingAdress): Response
    {
        return $this->render('billing_adress/show.html.twig', [
            'billing_adress' => $billingAdress,
        ]);
    }

    #[Route('/facturation/adresse/modifier/{id}', name: 'app_billing_adress_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, BillingAdress $billingAdress, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BillingAdressType::class, $billingAdress);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_billing_adress_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('billing_adress/edit.html.twig', [
            'billing_adress' => $billingAdress,
            'form' => $form,
        ]);
    }

    #[Route('/facturation/adresse/{id}', name: 'app_billing_adress_delete', methods: ['POST'])]
    public function delete(Request $request, BillingAdress $billingAdress, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$billingAdress->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($billingAdress);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_billing_adress_index', [], Response::HTTP_SEE_OTHER);
    }
}
