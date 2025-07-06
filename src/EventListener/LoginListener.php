<?php


// src/EventListener/LoginSuccessListener.php
namespace App\EventListener;

use App\Service\CartService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginSuccessListener
{
    private CartService $cartService;
    private RequestStack $requestStack;

    public function __construct(CartService $cartService, RequestStack $requestStack)
    {
        $this->cartService = $cartService;
        $this->requestStack = $requestStack;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        $session = $this->requestStack->getSession();

        // Récupérer le panier session
        $sessionCart = $session->get('cart', []);

        // Transférer dans le panier utilisateur
        foreach ($sessionCart as $productId => $quantity) {
            $this->cartService->add($productId, $quantity, $user);
        }

        // Nettoyer le panier session
        $session->remove('cart');
    }
}

