<?php

namespace MQM\ShopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class DiscountByProductRuleType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('startDate', 'date', array(
                'label' => 'add_fecha',
                'input' => 'datetime',
                'widget' => 'choice'
            ))
            ->add('deadline', 'date', array(
                'label' => 'add_fecha',
                'input' => 'datetime',
                'widget' => 'choice'
            ))
            ->add('discount', null, array(
                'label' => 'add_descuento'
            ))
        ;
    }

    public function getName()
    {
        return 'mqm_pricing_form_type_type_discount_by_product';
    }
    
    public function getDefaultOptions()
    {
        return array(
            'data_class' => 'MQM\PricingBundle\Entity\DiscountRule\DiscountByProductRule',
        );
    }
}
