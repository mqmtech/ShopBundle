<?php

namespace MQM\ShopBundle\Form\Shop;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use MQM\ShopBundle\Form\DataTransformer\PriceToPrettyPriceTransformer;
use MQM\ToolsBundle\Utils;

class PrettyPriceType extends AbstractType
{
    private $utils;
    
    public function __construct(Utils $utils)
    {
        $this->utils = $utils;
    }
    
    public function buildForm(FormBuilder $builder, array $options)
    {
        $transformer = new PriceToPrettyPriceTransformer($this->utils);
        $builder->appendClientTransformer($transformer);
    }
    
    public function getParent(array $options)
    {
        return 'text';
    }

    public function getName()
    {
        return 'mqm_shop_form_pretty_price';
    }
    
    public function getDefaultOptions()
    {
        return array(
            'data_class' => 'string',
            'invalid_message'=>'The value is not a valid price'
        );
    }
}