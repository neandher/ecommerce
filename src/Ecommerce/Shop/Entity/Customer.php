<?php

namespace App\Ecommerce\Shop\Entity;

use App\Component\Resource\Model\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Customer
 *
 * @ORM\Table(name="customer")
 * @ORM\Entity(repositoryClass="App\Ecommerce\Shop\Repository\CustomerRepository")
 * @UniqueEntity(fields={"email"}, message="user.email.already_exists")
 */
class Customer
{
    use TimestampableTrait;

    const UNKNOWN_GENDER = 'u';
    const MALE_GENDER = 'm';
    const FEMALE_GENDER = 'f';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $emailCanonical;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $lastName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $birthdayAt;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=1, options={"default": "u"})
     */
    private $gender = self::UNKNOWN_GENDER;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phoneNumber;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isSubscribedToNewsletter = false;

    /**
     * @var ShopUser
     *
     * @ORM\OneToOne(targetEntity="App\Ecommerce\Shop\Entity\ShopUser", mappedBy="customer", cascade={"all"})
     * @Assert\Valid()
     */
    protected $shopUser;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Customer
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set emailCanonical
     *
     * @param string $emailCanonical
     *
     * @return Customer
     */
    public function setEmailCanonical($emailCanonical)
    {
        $this->emailCanonical = $emailCanonical;

        return $this;
    }

    /**
     * Get emailCanonical
     *
     * @return string
     */
    public function getEmailCanonical()
    {
        return $this->emailCanonical;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Customer
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return Customer
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return trim(sprintf('%s %s', $this->firstName, $this->lastName));
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set birthdayAt
     *
     * @param \DateTime $birthdayAt
     *
     * @return Customer
     */
    public function setBirthdayAt($birthdayAt)
    {
        $this->birthdayAt = $birthdayAt;

        return $this;
    }

    /**
     * Get birthdayAt
     *
     * @return \DateTime
     */
    public function getBirthdayAt()
    {
        return $this->birthdayAt;
    }

    /**
     * Set gender
     *
     * @param string $gender
     *
     * @return Customer
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @return bool
     */
    public function isMale()
    {
        return self::MALE_GENDER === $this->gender;
    }

    /**
     * @return bool
     */
    public function isFemale()
    {
        return self::FEMALE_GENDER === $this->gender;
    }

    /**
     * Set phoneNumber
     *
     * @param string $phoneNumber
     *
     * @return Customer
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get phoneNumber
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set isSubscribedToNewsletter
     *
     * @param boolean $isSubscribedToNewsletter
     *
     * @return Customer
     */
    public function setIsSubscribedToNewsletter($isSubscribedToNewsletter)
    {
        $this->isSubscribedToNewsletter = $isSubscribedToNewsletter;

        return $this;
    }

    /**
     * Get isSubscribedToNewsletter
     *
     * @return bool
     */
    public function getIsSubscribedToNewsletter()
    {
        return $this->isSubscribedToNewsletter;
    }

    /**
     * @return ShopUser
     */
    public function getShopUser()
    {
        return $this->shopUser;
    }

    /**
     * @param ShopUser $shopUser
     * @return Customer
     */
    public function setShopUser($shopUser)
    {
        if ($this->shopUser !== $shopUser) {
            $this->shopUser = $shopUser;
            $this->assignCustomer($shopUser);
        }
        return $this;
    }

    /**
     * @param ShopUser|null $shopUser
     */
    protected function assignCustomer(ShopUser $shopUser = null)
    {
        if (null !== $shopUser) {
            $shopUser->setCustomer($this);
        }
    }
}

