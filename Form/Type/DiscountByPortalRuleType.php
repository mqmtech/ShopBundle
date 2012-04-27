<?php

namespace MQM\ShopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class DiscountByPortalRuleType extends AbstractType
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
            ->add('discount', 'mqm_shop.form.percentage', array(
                'label' => 'add_descuento'
            ))
        ;
    }

    public function getName()
    {
        return 'mqm_pricing_form_type_discount_by_portal';
    }

    public function getDefaultOptions()
    {
        return array(
            'data_class' => 'MQM\PricingBundle\FileSystem\DiscountRule\DiscountByPortalRule',
        );
    }
}
