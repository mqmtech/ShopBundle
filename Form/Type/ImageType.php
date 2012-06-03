<?php

namespace MQM\ShopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'hidden') //push the path (this way we can set the path before uploading an image)
            ->add('data', null, array(
                'label' => ' ',
                'data' => ' ',
            ))
        ;
    }

    public function getName()
    {
        return 'mqm_shop_form_type_image';
    }
    
    public function getDefaultOptions()
    {
        return array(
            'data_class' => 'MQM\ImageBundle\Entity\Image',
        );
    }
}
