<?php

namespace MQM\ShopBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use MQM\ToolsBundle\Utils;

class PriceToPrettyPriceTransformer implements DataTransformerInterface
{
    private $utils;

    public function __construct(Utils $utils)
    {
        $this->utils = $utils;
    }

    public function transform($val)
    {
        if (null === $val) {
            return '';
        }

        return $this->utils->floatToPrettyFloat($val);
    }

    public function reverseTransform($val)
    {
        if (!$val) {
            return null;
        }
        $val = substr($val, 0, count($val)-2);
        $float = $this->utils->prettyFloatToFloat($val);
        if (null === $float) {
            throw new TransformationFailedException(sprintf('An issue with number %s does not exist!', $val));
        }

        return $float;
    }
}


