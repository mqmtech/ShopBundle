<?php

namespace MQM\ShopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\PersistentCollection;
use MQM\CategoryBundle\Model\CategoryManagerInterface;

class CategoryType extends AbstractType
{
    private $categoryManager;
    
    public function buildForm(FormBuilderInterface $builder, array $options)
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
             'choices' => $this->buildOrdenedCategoriesChoice()
              ));
    }

    public function getName()
    {
        return 'mqm_shop_form_type_category';
    }
    
    public function buildOrdenedCategoriesChoice(PersistentCollection $categories=null, array &$categoriesChoice = null)
    {        
        if($categoriesChoice == null) {
            $categoriesChoice = array();
        }
        
        if ($categories == null) {
            $categories = (array) $this->categoryManager->findAllFamilies();
        }
        
        foreach ($categories as $category) {
            $categoriesChoice[$category->getId()] = $category;
            $subCategories = $category->getCategories();
            if($subCategories != null){
               $this->buildOrdenedCategoriesChoice($subCategories, $categoriesChoice);
            }
        }
        
        return $categoriesChoice;
    }
    
    public function getDefaultOptions()
    {
        return array(
            'data_class' => 'MQM\CategoryBundle\Entity\Category',
        );
    }
    
    public function __construct(CategoryManagerInterface $categoryManager)
    {
        $this->categoryManager = $categoryManager;
    }
    
    public function setCategoryManager(CategoryManagerInterface $categoryManager)
    {
        $this->categoryManager = $categoryManager;
    }
}
