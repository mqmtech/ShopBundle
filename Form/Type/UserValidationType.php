<?php

namespace MQM\ShopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class UserValidationType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('isEnabled', null, array(
                'required' => false,
            ))
        ;
    }

    public function getName()
    {
        return 'mqm_shop_form_type_user_validation';
    }
    
    public function getDefaultOptions()
    {
        return array(
            'data_class' => 'MQM\UserBundle\Entity\User',
            //'validation_groups' => 'Validation',
        );
    }
}
