<?php

namespace MQM\ShopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class BrandType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('name', null, array(
                'label' => 'add_categoria',
            ))
            ->add('description', null, array(
                'label' => 'descripcion_form'
            ))
            ->add('image', new ImageType(), array(
                'label' => ' '
            ))
        ;
    }

    public function getName()
    {
        return 'mqm_shop_form_type_brand';
    }
    
    public function getDefaultOptions()
    {
        return array(
            'data_class' => 'MQM\BrandBundle\Entity\Brand',
        );
    }
}
