<?php

namespace MQM\ShopBundle\Form\Shop;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\PersistentCollection;
use Symfony\Bundle\DoctrineBundle\Registry;

class CategoryType extends AbstractType
{
    private $doctrine;
    
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('name', null, array(
                'label' => 'add_categoria',
            ))
            ->add('description', null, array(
                'label' => 'descripcion_form',
            ))
            ->add('image', new ImageType(), array(
                'label' => ' '
            ))
            ->add('parentCategory', 'entity', array(
             'class' => 'MQM\CategoryBundle\Entity\Category',
             'empty_value' => 'Categorias...',
             'required' => false,
             'label' => 'Categoria',
             'choices' => $this->buildOrdenedCategoriesChoiceArray()
              ));
    }

    public function getName()
    {
        return 'tecnokey_shopbundle_shop_categorytype';
    }
    
    /**
     *
     * @param array $categories
     * @param array $categoriesChoice
     * @return type 
     */
    public function buildOrdenedCategoriesChoiceArray(PersistentCollection $categories=null, array &$categoriesChoice = null){
        
        if($categoriesChoice == null){
            $categoriesChoice = array();
        }
        
        if($this->doctrine == null){
            throw new \Symfony\Component\Config\Definition\Exception\Exception
            ("Custom Exception: No Doctrine passed throught the CategoryType constructor", null, null);
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
    
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'MQM\CategoryBundle\Entity\Category',
        );
    }
    
    public function __construct(Registry $doctrine) {
        $this->doctrine = $doctrine;
    }
    
    public function setDoctrine(Registry $doctrine){
        $this->doctrine = $doctrine;
    }
}
