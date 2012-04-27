<?php

namespace MQM\ShopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use MQM\ShopBundle\Form\DataTransformer\DecimalToPercentageTransformer;
use MQM\ToolsBundle\Utils;

class PercentageType extends AbstractType
{
    private $utils;
    
    public function __construct(Utils $utils)
    {
        $this->utils = $utils;
    }
    
    public function buildForm(FormBuilder $builder, array $options)
    {
        $transformer = new DecimalToPercentageTransformer($this->utils);
        $builder->appendClientTransformer($transformer);
    }
    
    public function getParent(array $options)
    {
        return 'text';
    }

    public function getName()
    {
        return 'mqm_shop_form_type_percentage';
    }
    
    public function getDefaultOptions()
    {
        return array(
            'data_class' => 'text',
            'invalid_message'=>'The value is not a valid number'
        );
    }
}