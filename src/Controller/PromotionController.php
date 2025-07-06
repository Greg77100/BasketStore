<?php

namespace App\Controller;

use App\Entity\Promotion;
use App\Form\PromotionType;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use App\Repository\PromotionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/promotion')]
final class PromotionController extends AbstractController{
    #[Route(name: 'app_promotion_index', methods: ['GET'])]
    public function index(PromotionRepository $promotionRepository): Response
    {
        return $this->render('promotion/index.html.twig', [
            'promotions' => $promotionRepository->findAll(),
        ]);
    }

    #[Route('/ajouter', name: 'app_promotion_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $promotion = new Promotion();
        $form = $this->createForm(PromotionType::class, $promotion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager->persist($promotion);
            $entityManager->flush();

            return $this->redirectToRoute('app_promotion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('promotion/new.html.twig', [
            'promotion' => $promotion,
            'form' => $form,
        ]);
    }

    #[Route('/fiche/{id}', name: 'app_promotion_show', methods: ['GET'])]
    public function show(Promotion $promotion): Response
    {
        return $this->render('promotion/show.html.twig', [
            'promotion' => $promotion,
        ]);
    }

    #[Route('/modifier/{id}', name: 'app_promotion_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Promotion $promotion, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PromotionType::class, $promotion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_promotion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('promotion/edit.html.twig', [
            'promotion' => $promotion,
            'form' => $form,
        ]);
    }

    #[Route('/supprimer/{id}', name: 'app_promotion_delete', methods: ['POST'])]
    public function delete(Request $request, Promotion $promotion, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$promotion->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($promotion);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_promotion_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/gestion', name: 'app_promotion_manage', methods: ['GET'])]
    public function manage(ProductRepository $productRepository,CategoryRepository $categoryRepository, PromotionRepository $promotionRepository): Response 
    {
        $products = $productRepository->findAll();
        $categories = $categoryRepository->findAll();
        $promotions = $promotionRepository->findAll();

        return $this->render('promotion/manage.html.twig', [
            'products' => $products,
            'categories' => $categories,
            'promotions' => $promotions,
        ]);
    }

    #[Route('/associer', name: 'app_promotion_associate', methods: ['POST'])]
    public function associatePromotions(Request $request,EntityManagerInterface $em,PromotionRepository $promotionRepository,ProductRepository $productRepository,CategoryRepository $categoryRepository): Response 
    {
        $promotionId = $request->request->get('selected_promotion');
        $productIds = $request->request->all('products');
        $categoryIds = $request->request->all('categories');

        if (!$promotionId) {
            $this->addFlash('danger', 'Veuillez sélectionner une promotion.');
            return $this->redirectToRoute('app_promotion_manage');
        }

        $promotion = $promotionRepository->find($promotionId);

        if (!$promotion) {
            $this->addFlash('danger', 'Promotion non trouvée.');
            return $this->redirectToRoute('app_promotion_manage');
        }

        $products = $productRepository->findBy(['id' => $productIds]);
        $categories = $categoryRepository->findBy(['id' => $categoryIds]);

        // Reset les anciennes associations si besoin
        foreach ($promotion->getProducts() as $product) {
            $promotion->removeProduct($product);
        }
        foreach ($promotion->getCategories() as $category) {
            $promotion->removeCategory($category);
        }

        // Ajout des nouvelles
        foreach ($products as $product) {
            $promotion->addProduct($product);
        }
        foreach ($categories as $category) {
            $promotion->addCategory($category);
        }

        $em->flush();

        $this->addFlash('success', 'Les éléments ont bien été associés à la promotion.');

        return $this->redirectToRoute('app_promotion_manage');
    }

    
}
