<?php

namespace MQM\ShopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use MQM\UserBundle\Model\UserInterface;

class StaffUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $builder->getData();
        $permissionType = $user->getPermissionType();

        $permissionsType = array();
        $permissionsType[$permissionType] = 'currentPermision';
        $permissionsType[UserInterface::ROLE_ADMIN] = 'Administrador';
        $permissionsType[UserInterface::ROLE_STAFF] = 'Staff';
        $permissionsType[UserInterface::ROLE_BASIC_STAFF] = 'Staff básico';
        
        $builder
            ->add('email', null, array(
                'required' => true,
            ));
            if ( $options['intention'] == 'StaffRevision' ) {
                $builder->add('password', 'password', array(
                    'required' => false,
                ));
            }
            else {
                $builder->add('password', 'password', array(
                    'required' => true,
                ));
            }
            $builder->add('permissionType', 'choice', array(
                'choices' => $permissionsType
                ))
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
