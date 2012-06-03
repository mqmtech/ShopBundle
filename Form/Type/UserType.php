<?php

namespace MQM\ShopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
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
            ));
            if ( $options['intention'] == 'Revision' ) {
                $builder->add('password', 'password', array(
                    'required' => false,
                ));
            }
            else {
                $builder->add('password', 'password', array(
                    'required' => true,
                ));
            }
            $builder->add('firmName', null, array(
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
        return 'mqm_shop_form_type_user';
    }
    
    public function getDefaultOptions()
    {
        return array(
            'data_class' => 'MQM\UserBundle\Entity\User',
            'validation_groups' => 'Registration',
        );
    }
}
