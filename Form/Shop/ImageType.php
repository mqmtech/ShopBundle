<?php

namespace MQM\ShopBundle\Form\Shop;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use MQM\ShopBundle\Form\Shop\CustomFileType;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
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
        return 'tecnokey_shopbundle_shop_imagetype';
    }
    
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'MQM\ImageBundle\Entity\Image',
        );
    }
}
