<?php

namespace MQM\ShopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\PersistentCollection;

use MQM\CategoryBundle\Model\CategoryManagerInterface;
use MQM\ShopBundle\Form\DataTransformer\PriceToPrettyPriceTransformer;

class ProductType extends AbstractType
{
    private $categoryManager;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $priceToPrettyPriceTransformer = new PriceToPrettyPriceTransformer(new \MQM\ToolsBundle\Utils());
        
        $builder
            ->add('id', 'hidden')
            ->add('name', null, array(
                'label' => 'add_titulo',
                'max_length' => '70'
            ))
            ->add('description')
            ->add('sku', null, array(
                'label' => 'add_ref'
            ))
            /*->add('basePrice', null, array(
            //->add('basePrice', 'mqm_shop.form.pretty_price', array(
                'label' => 'add_precio'
            ))*/
            ->add('priceRule', new PriceRuleType())
            ->add('discountRule', new DiscountByProductRuleType())
            ->add('category', 'entity', array(
             'class' => 'MQM\CategoryBundle\Entity\Category',
             'empty_value' => 'Categorias...',
             'required' => true,
             'label' => 'Categoria',
             'choices' => $this->buildOrdenedCategoriesChoice()
              ))
            ->add('image', new ImageType(), array(
                'required' => false,
                'label' => ' ',
            ))            
            ->add('secondImage', new ImageType(), array(
                'label' => ' '
            ))            
            ->add('thirdImage', new ImageType(), array(
                'label' => ' '
            ))
            ->add('fourthImage', new ImageType(), array(
                'label' => ' '
            ))            
            ->add('tag', null, array(
                'label' => ' add_tags'
            ))
            ->add('secondTag', null, array(
                'label' => ' add_tags'
            ))
            ->add('thirdTag', null, array(
                'label' => ' add_tags'
            ))
            ->add('fourthTag', null, array(
                'label' => ' add_tags'
            ))            
            ->add('brand', 'entity', array(
                'empty_value' => 'Marcas...',
                'class' => 'MQM\BrandBundle\Entity\Brand',
                'required' => true,
                'label' => 'Marca'
              ));
    }    

    public function buildOrdenedCategoriesChoice(PersistentCollection $categories=null, array &$categoriesChoice = null)
    {        
        if ($categoriesChoice == null) {
            $categoriesChoice = array();
        }
        
        if ($categories == null) {
            $categories = (array) $this->categoryManager->findAllFamilies();
        }
        
        foreach ($categories as $category) {
            $categoriesChoice[$category->getId()] = $category;
            $subCategories = $category->getCategories();
            if ($subCategories != null) {
               $this->buildOrdenedCategoriesChoice($subCategories, $categoriesChoice);
            }
        }
        
        return $categoriesChoice;
    }
    
    public function getName()
    {
        return 'mqm_shop_form_type_product';
    }
        
    public function getDefaultOptions()
    {
        return array(
            'data_class' => 'MQM\ProductBundle\Entity\Product',
            'validation_groups' => 'Registration',
        );
    }

    public function __construct(CategoryManagerInterface $categoryManager)
    {
            $this->categoryManager = $categoryManager;
    }
}
