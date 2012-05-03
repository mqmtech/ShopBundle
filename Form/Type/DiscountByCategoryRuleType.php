<?php

namespace MQM\ShopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use MQM\ShopBundle\Form\Type\DiscountRuleType;

class DiscountByCategoryRuleType extends DiscountRuleType
{
    public function getName()
    {
        return 'mqm_pricing_form_type_discount_by_category';
    }
    
    public function getDefaultOptions()
    {
        return array(
            'data_class' => 'MQM\PricingBundle\Entity\DiscountRule\DiscountByCategoryRule',
        );
    }
}