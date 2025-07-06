<?php

namespace App\Controller;

use App\Service\CartService;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    #[Route('/panier', name: 'cart_index')]
    public function index(CartService $cartService)
    {
        return $this->render('cart/index.html.twig', [
            'cart' => $cartService->getCart(),
            'total' => $cartService->getTotal()
        ]);
    }

    #[Route('/panier/ajouter/{id}', name: 'cart_add')]
public function add(CartService $cartService, int $id, Request $request, ProductRepository $productRepository): RedirectResponse
{
    $product = $productRepository->find($id);

    if (!$product) {
        $this->addFlash('danger', '❌ Produit introuvable.');
        return $this->redirect($request->headers->get('referer') ?? $this->generateUrl('app_catalog'));
    }

    $cartService->add($id);

    $this->addFlash('success', '✅ Produit "' . $product->getTitle() . '" ajouté au panier.');

    return $this->redirect($request->headers->get('referer') ?? $this->generateUrl('app_catalog'));
}

    #[Route('/panier/remove/{id}', name: 'cart_remove')]
    public function remove(CartService $cartService, int $id): RedirectResponse
    {
        $cartService->remove($id);
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/panier/modifier/{id}', name: 'cart_update', methods: ['POST'])]
    public function update(CartService $cartService, int $id, Request $request): RedirectResponse
    {
        $quantity = (int) $request->request->get('quantity');
        $cartService->updateQuantity($id, $quantity);
        return $this->redirectToRoute('cart_index');
    }
}