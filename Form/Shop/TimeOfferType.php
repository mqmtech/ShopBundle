<?php

namespace MQM\ShopBundle\Form\Shop;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class TimeDiscountRuleType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            //->add('name')
            //->add('description')
            //->add('scope')
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
        return 'tecnokey_shopbundle_shop_timediscountRuletype';
    }
    
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'MQM\ShopBundle\Entity\TimeDiscountRule',
        );
    }
}
