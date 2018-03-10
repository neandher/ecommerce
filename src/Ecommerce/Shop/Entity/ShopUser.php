<?php

namespace App\Ecommerce\Shop\Entity;

use App\Component\Core\Model\ShopUserInterface;
use Doctrine\ORM\Mapping as ORM;
use App\Component\User\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Ecommerce\Shop\Repository\ShopUserRepository")
 * @ORM\Table(name="shop_user")
 */
class ShopUser extends BaseUser implements ShopUserInterface
{
    /**
     * @var Customer
     *
     * @ORM\OneToOne(targetEntity="App\Ecommerce\Shop\Entity\Customer", inversedBy="shopUser", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid()
     */
    protected $customer;

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     * @return ShopUser
     */
    public function setCustomer($customer)
    {
        if ($this->customer != $customer) {
            $this->customer = $customer;
            $this->assignShopUser($customer);
            return $this;
        }
    }

    public function getEmail()
    {
        return $this->customer->getEmail();
    }

    public function setEmail($email)
    {
        return $this->customer->setEmail($email);
    }

    public function getEmailCanonical()
    {
        return $this->customer->getEmailCanonical();
    }

    public function setEmailCanonical($emailCanonical)
    {
        return $this->customer->setEmailCanonical($emailCanonical);
    }

    /**
     * @param Customer $customer
     */
    protected function assignShopUser($customer = null)
    {
        if (null !== $customer) {
            $customer->setShopUser($this);
        }
    }
}