<?php

namespace MQM\ShopBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use MQM\ToolsBundle\Utils;

class DecimalToPercentageTransformer implements DataTransformerInterface
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

        return $this->utils->decimalToPercentage($val);
    }

    public function reverseTransform($val)
    {
        if (!$val) {
            return null;
        }
        $decimal = $this->utils->percentageToDecimal($val);
        if (null === $decimal) {
            throw new TransformationFailedException(sprintf('An issue with number %s does not exist!', $val));
        }

        return $decimal;
    }
}
