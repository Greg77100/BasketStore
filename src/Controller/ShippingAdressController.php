<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\ShippingAdress;
use App\Form\ShippingAdressType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ShippingAdressRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


final class ShippingAdressController extends AbstractController{
    #[Route('/admin/livraison', name: 'app_shipping_adress_index', methods: ['GET'])]
    public function index(ShippingAdressRepository $shippingAdressRepository): Response
    {
        return $this->render('shipping_adress/index.html.twig', [
            'shipping_adress' => $shippingAdressRepository->findAll(),
        ]);
    }

    #[Route('/livraison/creation', name: 'app_shipping_adress_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser(); // Récupère l'utilisateur connecté
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour ajouter une adresse.');
        }

        $shippingAdress = new ShippingAdress();
        $shippingAdress->setUser($user); // Lier l'adresse à l'utilisateur connecté
        $form = $this->createForm(ShippingAdressType::class, $shippingAdress);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($shippingAdress);
            $entityManager->flush();

            $this->addFlash('success', 'Votre adresse de livraison a été ajoutée.');

            return $this->redirectToRoute('app_account', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('shipping_adress/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/livraison/voir/{id}', name: 'app_shipping_adress_show', methods: ['GET'])]
    public function show(ShippingAdress $shippingAdress): Response
    {
        return $this->render('shipping_adress/show.html.twig', [
            'shipping_adress' => $shippingAdress,
        ]);
    }

    /*#[Route('/{id}/edit', name: 'app_shipping_adress_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ShippingAdress $shippingAdress, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ShippingAdressType::class, $shippingAdress);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_shipping_adress_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('shipping_adress/edit.html.twig', [
            'shipping_adress' => $shippingAdress,
            'form' => $form,
        ]);
    }*/

    #[Route('/livraison/supprimer/{id}', name: 'app_shipping_adress_delete', methods: ['POST'])]
    public function delete(Request $request, ShippingAdress $shippingAdress, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$shippingAdress->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($shippingAdress);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_shipping_adress_index', [], Response::HTTP_SEE_OTHER);
    }
}
