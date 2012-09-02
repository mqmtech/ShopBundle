<?php

namespace MQM\ShopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class PriceRuleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('price', null, array(
                'label' => 'add_precio'
            ))
        ;
    }

    public function getName()
    {
        return 'mqm_pricing_form_type_type_price_rule_default';
    }
    
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'MQM\PricingBundle\Entity\PriceRule\PriceRule',
        );
    }
}
