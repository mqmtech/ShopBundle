<?php

namespace MQM\ShopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use MQM\ShopBundle\Form\Type\DiscountRuleType;

class DiscountByBrandRuleType extends DiscountRuleType
{
    public function getName()
    {
        return 'mqm_pricing_form_type_discount_by_brand';
    }
    
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'MQM\PricingBundle\Entity\DiscountRule\DiscountByBrandRule',
        );
    }
}
