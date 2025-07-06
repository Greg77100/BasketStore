<?php

namespace App\Entity;


use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 7, scale: 2, nullable: true)]
    private ?string $totalOrder = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $statutOrder = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $dateOrder = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    private ?Payment $payment = null;

    #[ORM\OneToOne(mappedBy: 'order', targetEntity: ShippingAdressOrder::class, cascade: ['persist', 'remove'])]
    private ?ShippingAdressOrder $shippingAdressOrder = null;

    #[ORM\OneToOne(mappedBy: 'order', targetEntity: BillingAdressOrder::class, cascade: ['persist', 'remove'])]
    private ?BillingAdressOrder $billingAdressOrder = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;
    /**
     * @var Collection<int, OrderList>
     */
    #[ORM\OneToMany(targetEntity: OrderList::class, mappedBy: 'order', cascade: ['persist'], orphanRemoval: true)]
    private Collection $orderList;

    public function __construct()
    {
        $this->orderList = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotalOrder(): ?string
    {
        return $this->totalOrder;
    }

    public function setTotalOrder(?string $totalOrder): static
    {
        $this->totalOrder = $totalOrder;

        return $this;
    }

    public function getStatutOrder(): ?string
    {
        return $this->statutOrder;
    }

    public function setStatutOrder(?string $statutOrder): static
    {
        $this->statutOrder = $statutOrder;

        return $this;
    }

    public function getDateOrder(): ?\DateTimeImmutable
    {
        return $this->dateOrder;
    }

    public function setDateOrder(?\DateTimeImmutable $dateOrder): static
    {
        $this->dateOrder = $dateOrder;

        return $this;
    }

    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    public function setPayment(?Payment $payment): static
    {
        $this->payment = $payment;

        return $this;
    }

    public function getShippingAdressOrder(): ?ShippingAdressOrder
    {
        return $this->shippingAdressOrder;
    }

    public function setShippingAdressOrder(?ShippingAdressOrder $shippingAdressOrder): static
    {
        $this->shippingAdressOrder = $shippingAdressOrder;

        return $this;
    }

    public function getBillingAdressOrder(): ?BillingAdressOrder
    {
        return $this->billingAdressOrder;
    }

    public function setBillingAdressOrder(?BillingAdressOrder $billingAdressOrder): static
    {
        $this->billingAdressOrder = $billingAdressOrder;

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
            $orderList->setOrders($this);
        }

        return $this;
    }

    public function removeOrderList(OrderList $orderList): static
    {
        if ($this->orderList->removeElement($orderList)) {
            // set the owning side to null (unless already changed)
            if ($orderList->getOrders() === $this) {
                $orderList->setOrders(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }
}
