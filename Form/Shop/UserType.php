<?php

namespace MQM\ShopBundle\Form\Shop;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class UserType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('firstName', null, array(
                'required' => true,
            ))
            ->add('lastName', null, array(
                'required' => true,
            ))
            ->add('email', null, array(
                'required' => true,
            ))
            ->add('address', null, array(
                'required' => true,
            ))
            ->add('password', 'password', array(
                'required' => true,
            ))
            ->add('firmName', null, array(
                'required' => true,
            ))
            ->add('vatin', null, array(
                'required' => true,
            ))
            ->add('zipCode', null, array(
                'required' => true,
            ))
            ->add('city', null, array(
                'required' => true,
            ))
            ->add('province', null, array(
                'required' => true,
            ))
            ->add('phone', null, array(
                'required' => true,
            ))
            ->add('fax')
        ;
    }

    public function getName()
    {
        return 'mqm_shop_form_user';
    }
    
    public function getDefaultOptions()
    {
        return array(
            'data_class' => 'MQM\UserBundle\Entity\User',
        );
    }
}
