<?php

namespace MQM\ShopBundle\Form\Shop;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\PersistentCollection;
use Symfony\Bundle\DoctrineBundle\Registry;

use MQM\ShopBundle\Form\DataTransformer\PriceToPrettyPriceTransformer;

class ProductType extends AbstractType
{

    /**
     *
     * @var Registry
     */
    public $doctrine = null;
    
    public function buildForm(FormBuilder $builder, array $options)
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
             'choices' => $this->buildOrdenedCategoriesChoiceArray()
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
    /**
     *
     * @param ArrayCollection $categories
     * @param array $categoriesChoice
     * @return array 
     */
    public function buildOrdenedCategoriesChoiceArray(PersistentCollection $categories=null, array &$categoriesChoice = null){
        
        if($categoriesChoice == null){
            $categoriesChoice = array();
        }
        
        if($categories == null){
            $categories = (array) $this->doctrine->getEntityManager()->getRepository('MQMCategoryBundle:Category')->findAllFamilies();
        }
        
        foreach ($categories as $category) {
            $categoriesChoice[$category->getId()] = $category;
            $subCategories = $category->getCategories();
            if($subCategories != null){
               $this->buildOrdenedCategoriesChoiceArray($subCategories, $categoriesChoice);
            }
        }
        
        return $categoriesChoice;
    }
    
    public function getName()
    {
        return 'tecnokey_shopbundle_shop_producttype';
    }
        
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'MQM\ProductBundle\Entity\Product',
        );
    }
    
    /**
     *
     * @param Registry $doctrine 
     */
    public function __construct(Registry $doctrine) {
            $this->doctrine = $doctrine;
    }
    
    /**
     *
     * @param Registry $doctrine 
     */
    public function setDoctrine(Registry $doctrine){
        $this->doctrine = $doctrine;               
    }
}
