<?php

namespace MQM\ShopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ShoppingCartItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantity', "integer")
        ;
    }

    public function getName()
    {
        return 'mqm_shop_form_type_cart_item';
    }
    
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'MQM\ShoppingCartBundle\Entity\ShoppingCartItem',
        );
    }
}
