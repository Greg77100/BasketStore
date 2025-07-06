<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Product;
use App\Entity\Wishlist;
use App\Service\CartService;
use App\Service\WishlistService;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/wishlist')]
final class WishlistController extends AbstractController
{
    public function __construct(
        private WishlistService $wishlistService,
        private Security $security
    ) {}

    #[Route('/', name: 'wishlist_index')]
    public function getWishlist(WishlistService $wishlistService, Security $security): Response
    {
        $user = $security->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir votre liste de souhait.');
        }

        $products = $wishlistService->getUserWishlistProducts($user);

        return $this->render('wishlist/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/ajouter/{id}', name: 'app_wishlist_add')]
    public function add(Product $product): Response
    {
        $user = $this->security->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour ajouter un produit à votre liste de souhait.');
        }

        $this->wishlistService->addProductToWishlist($user, $product);
        $this->addFlash('succes', 'Produit ajouté à votre liste.');

            return $this->redirectToRoute('wishlist_index');
        

       
    }

    #[Route('/deplacer/{id}', name: 'app_wishlist_remove')]
    public function remove(Product $product): Response
    {
        $user = $this->security->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour enlever un produit.');
        }

        $this->wishlistService->removeProductFromWishlist($user, $product);
        $this->addFlash('info', 'Produit retiré de votre liste de souhait.');

        return $this->redirectToRoute('wishlist_index');
    }

    #[Route('/toggle/{id}', name: 'wishlist_toggle', methods: ['POST'])]
    public function toggle(Product $product, WishlistService $wishlistService, EntityManagerInterface $entityManager): JsonResponse
    {
        
        /** @var User $user */
        $user = $this->getUser();

        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'Unauthoriezd'], 401);
        }

        $wishlist = $user->getWishlist();

        if (!$wishlist) {
            $wishlist = new Wishlist();
            $wishlist->setUser($user);
            $entityManager->persist($wishlist);
            $entityManager->flush();
            $user->setWishlist($wishlist);
        }

        if ($wishlist->getProducts()->contains($product)) {
            $wishlistService->removeProductFromWishlist($user, $product);
            return new JsonResponse(['status' => 'removed']);
        } else {
            $wishlistService->addProductToWishlist($user, $product);
            return new JsonResponse(['status' => 'added']);
        }
    }

    #[Route('/wishlist/ajouter-au-panier', name: 'wishlist_to_cart', methods: ['POST'])]
    public function wishlistToCart(CartService $cartService, EntityManagerInterface $em): RedirectResponse 
    {
        /** @var User $user */
        $user = $this->getUser();

        $wishlist = $user->getWishlist();

        if (!$user || !$wishlist || $wishlist->getProducts()->isEmpty()) {
            $this->addFlash('warning', 'Votre liste de souhaits est vide.');
            return $this->redirectToRoute('wishlist_index');
        }

        foreach ($wishlist->getProducts() as $product) {
            $cartService->add($product->getId());
        }

        // 💥 On vide la wishlist
        $wishlist->getProducts()->clear(); // retire tous les produits
        $em->flush();

        $this->addFlash('success', 'Tous les produits ont été ajoutés au panier.');

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/wishlist/ajouter-au-panier/{id}', name: 'wishlist_add_to_cart', methods: ['POST'])]
    public function wishlistAddToCart(int $id, CartService $cartService, EntityManagerInterface $em, ProductRepository $productRepository): RedirectResponse 
    {
        /** @var User $user */
        $user = $this->getUser();
        $product = $productRepository->find($id);

        if (!$user || !$user->getWishlist() || !$product) {
            $this->addFlash('warning', 'Impossible d\'ajouter ce produit.');
            return $this->redirectToRoute('wishlist_index');
        }

        $cartService->add($id);

        // 💥 Retirer le produit de la wishlist
        $wishlist = $user->getWishlist();
        $wishlist->removeProduct($product); // Assure-toi que cette méthode existe
        $em->flush();

        $this->addFlash('success', 'Produit ajouté au panier.');

        return $this->redirectToRoute('wishlist_index');
    }



}
