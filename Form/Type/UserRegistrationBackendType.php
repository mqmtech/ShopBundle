<?php

namespace MQM\ShopBundle\Form\Type;

use MQM\UserBundle\Form\Type\UserRegistrationType;
use MQM\ShopBundle\Form\Type\DiscountByUserRuleType;
use Symfony\Component\Form\FormBuilderInterface;

class UserRegistrationBackendType extends UserRegistrationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $discountByUserRuleType = new DiscountByUserRuleType();
        parent::buildForm($builder, $options);
        $builder
            ->add('discountRule', $discountByUserRuleType, array(
                'required' => false,
            ))
            ;
    }

    public function getName()
    {
        return 'mqm_user_form_type_registration_client';
    }
    
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'MQM\UserBundle\Entity\User',
        );
    }
}
