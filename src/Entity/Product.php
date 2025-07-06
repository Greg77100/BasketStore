<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(nullable: true)]
    private ?float $price = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?Category $category = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?Brand $brand = null;

    

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'product')]
    private Collection $comments;

   

    #[ORM\Column(nullable: true)]
    private ?int $stock = null;
   
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $addedAt = null;

    /**
     * @var Collection<int, Wishlist>
     */
    #[ORM\ManyToMany(targetEntity: Wishlist::class, mappedBy: 'products')]
    private Collection $wishlists;

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?Promotion $promotion = null;

    /**
     * @var Collection<int, OrderList>
     */
    #[ORM\OneToMany(targetEntity: OrderList::class, mappedBy: 'product')]
    private Collection $orderList;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $picture = null;

    /**
     * @var Collection<int, CartItem>
     */
    #[ORM\OneToMany(targetEntity: CartItem::class, mappedBy: 'product')]
    private Collection $cartItems;

    public function __construct()
    {
        $this->addedAt = new \DateTimeImmutable();
        $this->comments = new ArrayCollection();
        $this->wishlists = new ArrayCollection();
        $this->orderList = new ArrayCollection();
        $this->cartItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

   

    

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setProduct($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getProduct() === $this) {
                $comment->setProduct(null);
            }
        }

        return $this;
    }

   

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(?int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    public function getAddedAt(): ?\DateTimeImmutable
    {
        return $this->addedAt;
    }

    public function setAddedAt(?\DateTimeImmutable $addedAt): static
    {
        $this->addedAt = $addedAt;

        return $this;
    }

    /**
     * @return Collection<int, Wishlist>
     */
    public function getWishlists(): Collection
    {
        return $this->wishlists;
    }

    public function addWishlist(Wishlist $wishlist): static
    {
        if (!$this->wishlists->contains($wishlist)) {
            $this->wishlists[] = $wishlist;
           
        }

        return $this;
    }

    public function removeWishlist(Wishlist $wishlist): static
    {
        $this->wishlists->removeElement($wishlist);
        return $this;
             
        
    }

    public function getPromotion(): ?Promotion
    {
        return $this->promotion;
    }

    public function setPromotion(?Promotion $promotion): static
    {
        $this->promotion = $promotion;

        return $this;
    }

    /**
     * @return Collection<int, OrderList>
     */
    public function getOrderList(): Collection
    {
        return $this->orderList;
    }

    public function addOrderList(OrderList $orderList): static
    {
        if (!$this->orderList->contains($orderList)) {
            $this->orderList->add($orderList);
            $orderList->setProduct($this);
        }

        return $this;
    }

    public function removeOrderList(OrderList $orderList): static
    {
        if ($this->orderList->removeElement($orderList)) {
            // set the owning side to null (unless already changed)
            if ($orderList->getProduct() === $this) {
                $orderList->setProduct(null);
            }
        }

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * @return Collection<int, CartItem>
     */
    public function getCartItems(): Collection
    {
        return $this->cartItems;
    }

    public function addCartItem(CartItem $cartItem): static
    {
        if (!$this->cartItems->contains($cartItem)) {
            $this->cartItems->add($cartItem);
            $cartItem->setProduct($this);
        }

        return $this;
    }

    public function removeCartItem(CartItem $cartItem): static
    {
        if ($this->cartItems->removeElement($cartItem)) {
            // set the owning side to null (unless already changed)
            if ($cartItem->getProduct() === $this) {
                $cartItem->setProduct(null);
            }
        }

        return $this;
    }

    public function isOnPromotion(): bool
    {
        $now = new \DateTime();

        foreach ($this->getPromotion() as $promotion) {
            if ($promotion->getStartDate() <= $now && $promotion->getEndDate() >= $now) {
                return true;
            }
        }

        return false;
    }

    public function getDiscountedPrice(): ?float
    {
        $now = new \DateTime();

        foreach ($this->getPromotion() as $promotion) {
            if ($promotion->getStartDate() <= $now && $promotion->getEndDate() >= $now) {
                return $this->price * (1 - $promotion->getDiscountPercentage() / 100);
            }
        }

        return null;
    }


}
