<?php

namespace MQM\ShopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use MQM\ShopBundle\Form\DataTransformer\PriceToPrettyPriceTransformer;
use MQM\ToolsBundle\Utils;

class PrettyPriceType extends AbstractType
{
    private $utils;
    
    public function __construct(Utils $utils)
    {
        $this->utils = $utils;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new PriceToPrettyPriceTransformer($this->utils);
        $builder->appendClientTransformer($transformer);
    }
    
    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'mqm_shop_type_pretty_price';
    }
    
    public function getDefaultOptions(array $options)
    {
        return array(
            'invalid_message'=>'The value is not a valid price'
        );
    }
}