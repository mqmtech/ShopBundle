<?php

namespace MQM\ShopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class NewsLetterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, array(
                'required' => true,
            ))
            ->add('body', null, array(
            'required' => true,
            ))
        ;
    }

    public function getName()
    {
        return 'mqm_shop_form_type_user';
    }
    
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'MQM\NewsLetterBundle\Entity\NewsLetter',
        );
    }
}
