<?php

namespace App\Service;

use App\Entity\CartItem;

use App\Repository\ProductRepository;
use App\Repository\CartItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    private $session;
    private $productRepository;
    private $cartItemRepository;
    private $entityManager;
    private $security;
    

    public function __construct(
        SessionInterface $session,
        ProductRepository $productRepository,
        CartItemRepository $cartItemRepository,
        EntityManagerInterface $entityManager,
        Security $security
        
    ) {
        $this->session = $session;
        $this->productRepository = $productRepository;
        $this->cartItemRepository = $cartItemRepository;
        $this->entityManager = $entityManager;
        $this->security = $security;
        
    }

    public function add(int $productId, int $quantity = 1)
    {
        $user = $this->security->getUser();

        if ($user) {
            // Stockage en base de données
            $cartItem = $this->cartItemRepository->findOneBy([
                'user' => $user,
                'product' => $this->productRepository->find($productId)
            ]);

            if (!$cartItem) {
                $cartItem = new CartItem();
                $cartItem->setUser($user);
                $cartItem->setProduct($this->productRepository->find($productId));
                $cartItem->setQuantity($quantity);
                $this->entityManager->persist($cartItem);
            } else {
                $cartItem->setQuantity($cartItem->getQuantity() + $quantity);
            }

            $this->entityManager->flush();
        } else {
            // Stockage en session
            $cart = $this->session->get('cart', []);

            if (!isset($cart[$productId])) {
                $cart[$productId] = $quantity;
            } else {
                $cart[$productId] += $quantity;
            }

            $this->session->set('cart', $cart);
        }
    }

    public function remove(int $productId)
    {
        $user = $this->security->getUser();

        if ($user) {
            // Supprimer de la base de données
            $cartItem = $this->cartItemRepository->findOneBy([
                'user' => $user,
                'product' => $productId
            ]);

            if ($cartItem) {
                $this->entityManager->remove($cartItem);
                $this->entityManager->flush();
            }
        } else {
            // Supprimer de la session
            $cart = $this->session->get('cart', []);
            unset($cart[$productId]);
            $this->session->set('cart', $cart);
        }
    }

    public function updateQuantity(int $productId, int $quantity)
    {
        $user = $this->security->getUser();

        if ($user) {
            $cartItem = $this->cartItemRepository->findOneBy([
                'user' => $user,
                'product' => $productId
            ]);

            if ($cartItem) {
                $cartItem->setQuantity($quantity);
                $this->entityManager->flush();
            }
        } else {
            $cart = $this->session->get('cart', []);
            if (isset($cart[$productId])) {
                $cart[$productId] = $quantity;
            }
            $this->session->set('cart', $cart);
        }
    }

    public function getCart()
    {
        $user = $this->security->getUser();
        $cartWithData = [];

        if ($user) {
            $cartItems = $this->cartItemRepository->findBy(['user' => $user]);

            foreach ($cartItems as $cartItem) {
                $cartWithData[] = [
                    'product' => $cartItem->getProduct(),
                    'quantity' => $cartItem->getQuantity()
                ];
            }
        } else {
            $cart = $this->session->get('cart', []);
            foreach ($cart as $id => $quantity) {
                $cartWithData[] = [
                    'product' => $this->productRepository->find($id),
                    'quantity' => $quantity
                ];
            }
        }

        return $cartWithData;
    }

    public function getTotal(): float
    {
        $total = 0;
        foreach ($this->getCart() as $item) {
            $total += $item['product']->getPrice() * $item['quantity'];
        }
        return $total;
    }
    
    public function getCartCount(): int
    {
        $user = $this->security->getUser();

        if ($user) {
            return $this->cartItemRepository->count(['user' => $user]);
        }

        return count($this->session->get('cart', []));
    }

    public function clearCart(): void
    {
        $user = $this->security->getUser();

        if ($user) {
            $cartItems = $this->cartItemRepository->findBy(['user' => $user]);

            foreach ($cartItems as $item) {
                $this->entityManager->remove($item);
            }

            $this->entityManager->flush();
        } else {
            $this->session->remove('cart');
    }
    }

    public function transferSessionCartToUser(): void
{
    $user = $this->security->getUser();
    if (!$user) {
        return;
    }

    // Si la session a été réinitialisée, on récupère le panier de secours
    $sessionCart = $this->session->get('cart_backup', []);
    if (empty($sessionCart)) {
        $sessionCart = $this->session->get('cart', []);
    }

    if (empty($sessionCart)) {
        return;
    }

    foreach ($sessionCart as $productId => $quantity) {
        $cartItem = $this->cartItemRepository->findOneBy([
            'user' => $user,
            'product' => $productId
        ]);

        if (!$cartItem) {
            $product = $this->productRepository->find($productId);
            if (!$product) continue;

            $cartItem = new CartItem();
            $cartItem->setUser($user);
            $cartItem->setProduct($product);
            $cartItem->setQuantity($quantity);
            $this->entityManager->persist($cartItem);
        } else {
            $cartItem->setQuantity($cartItem->getQuantity() + $quantity);
        }
    }

    $this->entityManager->flush();

    // Nettoyage des paniers session
    $this->session->remove('cart');
    $this->session->remove('cart_backup');
}




}
