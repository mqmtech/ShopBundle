<?php

namespace MQM\ShopBundle\Form\Type\Model;

use MQM\UserBundle\Model\UserInterface;
use MQM\PricingBundle\Model\DiscountRule\DiscountRuleInterface;

class UserValidationWithDiscount
{
    private $user;
    private $discountRule;

    public function __construct(UserInterface $user, DiscountRuleInterface $discountRule)
    {
        $this->user = $user;
        $this->discountRule = $discountRule;
    }

    public function setDiscountRule($discountRule)
    {
        $this->discountRule = $discountRule;
    }

    public function getDiscountRule()
    {
        return $this->discountRule;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }
}