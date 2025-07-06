<?php

namespace App\Twig;

use Twig\TwigFunction;
use App\Entity\Product;
use App\Entity\Wishlist;
use Twig\Extension\AbstractExtension;

class WishlistExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('productnWishlist', [$this, 'productInWishlist']),
        ];
    }

    public function productInWishlist(Product $product, ?Wishlist $wishlist): bool
    {
        if (!$wishlist)  return false;

        return $wishlist->getProducts()->contains($product);
    }
}