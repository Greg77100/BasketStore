<?php


namespace App\Form\Model;

use App\Entity\ShippingAdressOrder;
use App\Entity\BillingAdressOrder;

class AdressData
{
    public ?ShippingAdressOrder $shipping = null;
    public ?BillingAdressOrder $billing = null;
    public bool $sameAdress = false;

    public function getShippingAdress(): ?ShippingAdressOrder
    {
        return $this->shipping;
    }

    public function setShippingAdress(?ShippingAdressOrder $shippingAdress): void
    {
        $this->shipping = $shippingAdress;
    }

    public function getBillingAdress(): ?BillingAdressOrder
    {
        return $this->billing;
    }

    public function setBillingAdress(?BillingAdressOrder $billingAdressOrder): void
    {
        $this->billing = $billingAdressOrder;
    }

    public function getSameAdress(): bool
    {
        return $this->sameAdress;
    }

    public function setSameAdress(bool $sameAdress): void
    {
        $this->sameAdress = $sameAdress;
    }
}
