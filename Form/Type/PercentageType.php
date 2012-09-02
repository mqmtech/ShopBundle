<?php

namespace MQM\ShopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use MQM\ShopBundle\Form\DataTransformer\DecimalToPercentageTransformer;
use MQM\ToolsBundle\Utils;

class PercentageType extends AbstractType
{
    private $utils;
    
    public function __construct(Utils $utils)
    {
        $this->utils = $utils;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new DecimalToPercentageTransformer($this->utils);
        $builder->appendClientTransformer($transformer);
    }
    
    public function getParent()
    {
        return 'field';
    }

    public function getName()
    {
        return 'mqm_shop_form_type_percentage';
    }
    
    public function getDefaultOptions(array $options)
    {
        return array(
            'invalid_message'=>'The value is not a valid number'
        );
    }
}