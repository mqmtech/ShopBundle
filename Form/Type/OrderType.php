<?php

namespace MQM\ShopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use MQM\OrderBundle\Model\OrderInterface;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('status', 'choice', array(
                'choices' => array(
                    OrderInterface::STATUS_0_RECEIVED => 'Recibido' ,
                    OrderInterface::STATUS_1_IN_PROCESS => 'En Proceso',
                    OrderInterface::STATUS_2_DELIVERED => 'Enviado'
                ))
            )
        ;
    }

    public function getName()
    {
        return 'mqm_shop_form_type_order';
    }
}
