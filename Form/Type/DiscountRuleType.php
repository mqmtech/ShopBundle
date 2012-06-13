<?php

namespace MQM\ShopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class DiscountRuleType extends AbstractType
{    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $defaultStartDate = new \DateTime('now');
        $defaultDeadline = new \DateTime('now + 4 year');        
        $this->buildDate($builder, 'startDate', $defaultStartDate);
        $this->buildDate($builder, 'deadline', $defaultDeadline)
            ->add('discount', 'mqm_shop.form.percentage', array(
                'label' => 'add_descuento'
            ))
        ;
    }
    
    private function buildDate($builder, $fieldName, $defaultDate = null)
    {
        if (!$defaultDate) {
            $defaultDate = new \DateTime('now');
        }
        
        $builder->add($fieldName, 'date', array(
                'label' => 'add_fecha',
                'input' => 'datetime',
                'widget' => 'choice',
                'empty_value' => array(
                    'day' =>$defaultDate->format('d'), 
                    'month' => $defaultDate->format('m'),
                    'year' => $defaultDate->format('Y'),
                ),
                'format' => 'dd-MM-yyyy'
            ))
        
                ;
        return $builder;
    }

    public function getName()
    {
        return 'mqm_pricing_form_type_discount';
    }

    public function getDefaultOptions()
    {
        return array(
            'data_class' => 'MQM\PricingBundle\Model\DiscountRule\DiscountRuleInterface',
        );
    }
}
