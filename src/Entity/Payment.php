<?php

namespace App\Entity;

use App\Repository\PaymentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $datePayment = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $method = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $statutPayment = null;

    #[ORM\Column(type: 'float')]
    private ?float $amount = null;

    /**
     * @var Collection<int, Order>
     */
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'payment')]
    private Collection $orders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatePayment(): ?\DateTimeImmutable
    {
        return $this->datePayment;
    }

    public function setDatePayment(?\DateTimeImmutable $datePayment): static
    {
        $this->datePayment = $datePayment;

        return $this;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->method;
    }

    public function setPaymentMethod(?string $method): static
    {
        $this->method = $method;

        return $this;
    }

    public function getStatutPayment(): ?string
    {
        return $this->statutPayment;
    }

    public function setStatutPayment(?string $statutPayment): static
    {
        $this->statutPayment = $statutPayment;

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): static
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setPayment($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): static
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getPayment() === $this) {
                $order->setPayment(null);
            }
        }

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;
        return $this;
    }
}
