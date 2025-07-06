<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Form\BrandType;
use App\Repository\BrandRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/marque')]
final class BrandController extends AbstractController
{
    #[Route(name: 'app_brand_index', methods: ['GET'])]
    public function index(BrandRepository $brandRepository): Response
    {
        return $this->render('brand/index.html.twig', [
            'brands' => $brandRepository->findAll(),
        ]);
    }

    #[Route('/ajouter', name: 'app_brand_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $brand = new Brand();
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($brand);
            $entityManager->flush();
            $this->addFlash('success', 'La marque a bien été ajoutée');
            return $this->redirectToRoute('app_brand_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('brand/new.html.twig', [
            'brand' => $brand,
            'form' => $form,
        ]);
    }

    #[Route('fiche/{id}', name: 'app_brand_show', methods: ['GET'])]
    public function show(Brand $brand): Response
    {
        return $this->render('brand/show.html.twig', [
            'brand' => $brand,
        ]);
    }

    #[Route('/modifier/{id}', name: 'app_brand_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Brand $brand, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'La marque a bien été modifiée');
            return $this->redirectToRoute('app_brand_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('brand/edit.html.twig', [
            'brand' => $brand,
            'form' => $form,
        ]);
    }

    #[Route('supprimer/{id}', name: 'app_brand_delete', methods: ['POST'])]
    public function delete(Request $request, Brand $brand, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$brand->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($brand);
            $entityManager->flush();
            $this->addFlash('success', 'La marque a bien été supprimée');
        }

        return $this->redirectToRoute('app_brand_index', [], Response::HTTP_SEE_OTHER);
    }
}
