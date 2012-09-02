<?php

namespace MQM\ShopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use MQM\ShopBundle\Form\Type\DiscountRuleType;

class DiscountByUserRuleType extends DiscountRuleType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('extraDiscount', 'mqm_shop.form.percentage', array(
            'label' => 'add_descuento'
        ))
        ;
    }

    public function getName()
    {
        return 'mqm_pricing_form_type_discount_by_user';
    }
    
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'MQM\PricingBundle\Entity\DiscountRule\DiscountByUserRule',
        );
    }
}
