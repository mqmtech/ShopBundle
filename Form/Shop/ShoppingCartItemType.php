<?php

namespace MQM\ShopBundle\Form\Shop;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ShoppingCartItemType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            //->add('product', 'form.type.shoppingcart_product',array(
              //  'read_only' => true    
                //))
            //->add('basePrice', 'text', array(
             //   'read_only' => true
            //))
            //->add('totalBasePrice', 'text', array(
            //    'read_only' => true
            //))
            ->add('quantity', "integer", array(
                
            ))            
        ;
    }

    public function getName()
    {
        return 'mqm_shop_form_cart_item';
    }
    
    public function getDefaultOptions()
    {
        return array(
            'data_class' => 'MQM\ShoppingCartBundle\Entity\ShoppingCartItem',
        );
    }
}
