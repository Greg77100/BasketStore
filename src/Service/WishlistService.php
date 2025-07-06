<?php

namespace App\Service;

use App\Entity\Product;
use App\Entity\User;
use App\Entity\Wishlist;
use Doctrine\ORM\EntityManagerInterface;

class WishlistService
{

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function addProductToWishlist(User $user, Product $product): void
    {
        $wishlist =$user->getWishlist();

        if (!$wishlist) {
            $wishlist = new Wishlist();
            $wishlist->setUser($user);
            $user->setWishlist($wishlist);
            $this->entityManager->persist($wishlist);
        }

        if (!$wishlist->getProducts()->contains($product)) {
            $wishlist->addProduct($product);
        }

        $this->entityManager->persist($wishlist);
        $this->entityManager->flush(); 
    }

    public function removeProductFromWishlist(User $user, Product $product): void
    {
        $wishlist = $user->getWishlist();

        if ($wishlist && $wishlist->getProducts()->contains($product)) {
            $wishlist->removeProduct($product);
            $this->entityManager->persist($wishlist);
            $this->entityManager->flush();
        }
    }

    public function getUserWishlistProducts(User $user): array
    {
        $wishlist = $user->getWishlist();

        if (!$wishlist) {
            return [];
        }
        return $wishlist->getProducts()->toArray();
    }

    

}