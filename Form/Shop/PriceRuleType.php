<?php

namespace MQM\ShopBundle\Form\Shop;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class PriceRuleType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('price', null, array(
                'label' => 'add_precio'
            ))
        ;
    }

    public function getName()
    {
        return 'mqm_pricing_form_type_price_rule_default';
    }
    
    public function getDefaultOptions()
    {
        return array(
            'data_class' => 'MQM\PricingBundle\Entity\PriceRule\PriceRule',
        );
    }
}
