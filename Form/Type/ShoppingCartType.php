<?php

namespace MQM\ShopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ShoppingCartType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('items', 'collection', array(
                'type' => new ShoppingCartItemType(),
                'label' => ' ',
            ))
        ;
    }

    public function getName()
    {
        return 'mqm_shop_form_type_cart';
    }
    
    public function getDefaultOptions()
    {
        return array(
            'data_class' => 'MQM\ShoppingCartBundle\Entity\ShoppingCart',
        );
    }
}
