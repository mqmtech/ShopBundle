<?php

namespace MQM\ShopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class TimeDiscountRuleType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('start', 'date', array(
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
        return 'mqm_shop_form_type_time_discount_rule';
    }
    
    public function getDefaultOptions()
    {
        return array(
            'data_class' => 'MQM\ShopBundle\Entity\TimeDiscountRule',
        );
    }
}
