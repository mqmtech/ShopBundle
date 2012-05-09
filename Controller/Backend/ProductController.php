<?php

namespace MQM\ShopBundle\Controller\Backend;

use MQM\ProductBundle\Entity\Product;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Frontend\Default controller.
 *
 * @Route("/admin/productos")
 */
class ProductController extends Controller {

    /**
     * Backend Products
     *
     * @Route("/", name="TKShopBackendProductsIndex")
     * @Template()
     */
    public function indexAction() {
        return array();        
    }
    
    /**
     * Backend product
     *
     * @Route("/~/nuevo", name="TKShopBackendProductNew")
     * @Template()
     */
    public function newAction() {

        $entity = $this->get('mqm_product.product_manager')->createProduct();
        $productType = $this->get('mqm_shop.form.product');
        $form   = $this->createForm($productType, $entity);        

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );        
    }
    
    /**
     * Creates a new Shop\Product entity.
     *
     * @Route("/create", name="TKShopBackendProductCreate")
     * @Method("post")
     * @Template("MQMShopBundle:Backend\Product:new.html.twig")
     */
    public function createAction()
    {
        $entity  = $this->get('mqm_product.product_manager')->createProduct();
        $request = $this->getRequest();
        $productType = $this->get('mqm_shop.form.product');
        $form   = $this->createForm($productType, $entity);
        $form->bindRequest($request);

        if ($form->isValid()) { // Sometimes there have been errors due to date format
            $this->get('mqm_product.product_manager')->saveProduct($entity);
            
            return $this->redirect($this->generateUrl('TKShopBackendProductsShowAll'));            
        }
        
        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }
    
    /**
     * @Route("/{productId}/editar", name="TKShopBackendProductEdit")
     * @Template("MQMShopBundle:Backend\Product:edit.html.twig")
     */
    public function editAction($productId)
    {
        $entity = $this->get('mqm_product.product_manager')->findProductBy(array('id' => $productId));
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Shop\Product entity.');
        }
        
        /**
         * TODO: Create service Layer to guarantee data integrity
         * example: Verify that a product has an discountRule ( or create a void discountRule ) before load the form )
         */
         
         if($entity->getDiscountRule() == null){
             $entity->setDiscountRule(new \MQM\PricingBundle\Entity\DiscountRule\DiscountByProductRule());
         }
                
        $productType = $this->get('mqm_shop.form.product');
        $editForm   = $this->createForm($productType, $entity);
        $deleteForm = $this->createDeleteForm($productId);

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView()
        );        
    }
    
    /**
     * Displays a form to edit an existing Shop\Product entity.
     *
     * @Route("/{productId}/clonar", name="TKShopBackendProductClone")
     * @Template("MQMShopBundle:Backend\Product:new.html.twig")
     */
    public function cloneAction($productId)
    {
        $entity = $this->get('mqm_product.product_manager')->findProductBy(array('id' => $productId));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Shop\Product entity.');
        }
        $entityCloned = clone ($entity);
        $productType = $this->get('mqm_shop.form.product');
        $editForm   = $this->createForm($productType, $entityCloned);

        return array(
            'entity'      => $entityCloned,
            'form'   => $editForm->createView(),
        );
        
    }
    
    /**
     * Deletes a Shop\Product entity.
     *
     * @Route("/{productId}/eliminar", name="TKShopBackendProductDelete")
     * @Method("post")
     */
    public function deleteAction($productId)
    {
        $form = $this->createDeleteForm($productId);
        $request = $this->getRequest();
        $form->bindRequest($request);
        
        if (false || $form->isValid()) { //// TRUE value is a  TEMPORAL SOLUTION OF DATETIME PROBLEM-> SOLVE SOON  ////
            $entity = $this->get('mqm_product.product_manager')->findProductBy(array('id' => $productId));
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Shop\Product entity.');
            }
            $this->get('mqm_product.product_manager')->deleteProduct($entity);
                    
            return $this->redirect($this->generateUrl('TKShopBackendProductsShowAll'));
        }
        else {
            $content = $this->renderView('MQMShopBundle:Backend\Product:index.html.twig', array('error' => 'El producto no puede ser eliminado'));
            return $content;
        }
    }
    
    /**
     * Edits an existing Shop\Product entity.
     *
     * @Route("/{productId}/actualizar", name="TKShopBackendProductUpdate")
     * @Method("post")
     * @Template("MQMShopBundle:Backend\Product:edit.html.twig")
     */
    public function updateAction($productId)
    {
        $entity = $this->get('mqm_product.product_manager')->findProductBy(array('id' => $productId));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Shop\Product entity.');
        }        

        $productType = $this->get('mqm_shop.form.product');
        $editForm   = $this->createForm($productType, $entity);
        $request = $this->getRequest();
        $editForm->bindRequest($request);
        if ($editForm->isValid()) {//// TRUE value is a  TEMPORAL SOLUTION OF DATETIME PROBLEM-> SOLVE SOON  ////            
            //FIX VIRTUAL file update in Image
            $image = $entity->getImage();
            if ($image != null) {
                if ($image->isFileUpdated() == false) {
                    $image->setFileUpdated(true);
                }
                else {
                    $image->setFileUpdated(false);
                }
            }

            $image = $entity->getSecondImage();
            if($image != null){
                if($image->isFileUpdated() == false){
                    $image->setFileUpdated(true);
                }
                else{
                    $image->setFileUpdated(false);
                }
            }

            $image = $entity->getThirdImage();
            if($image != null){
                if($image->isFileUpdated() == false){
                    $image->setFileUpdated(true);
                }
                else{
                    $image->setFileUpdated(false);
                }
            }

            $image = $entity->getFourthImage();
            if($image != null){
                if($image->isFileUpdated() == false){
                    $image->setFileUpdated(true);
                }
                else{
                    $image->setFileUpdated(false);
                }
            }
            //End FIX VIRTUAL file update in Image
            
            // Set Product to the discount-> this is necessary for the ORM as it's the discount who stores the productId     
            $discountRule = $entity->getDiscountRule();
            if ($discountRule != null) {
                $discountRule->setProduct($entity);
            }
            // End set Product to the discount
            $this->get('mqm_product.product_manager')->saveProduct($entity);

            return $this->redirect($this->generateUrl('TKShopBackendProductsShowAll'));
        }
        
        throw new \Exception("Custom Exception: Form not validated");

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    
     /**
     * @Route("/administracion/ver_todos", name="TKShopBackendProductsShowAll")
     * @Template()
     */
    public function showAllAction()
    {
        $paginationManager = $this->get('mqm_pagination.pagination_manager'); 
        $products = $this->get('mqm_product.product_manager')->findProducts($paginationManager);        
        $deleteForms = array();
        foreach ($products as $product) {
            $form = $this->createDeleteForm($product->getId());
            $deleteForms[$product->getId()] = $form->createView();
        }
        
        return array(
            'products' => $products,
            'deleteForms' => $deleteForms,
        );
    }
    
    /**
     * @Route("/administracion/por_categoria", name="TKShopBackendProductsCategoriesShowAll")
     * @Template("MQMShopBundle:Backend\Product:showAllCategories.html.twig")
     */
    public function showAllCategoriesAction()
    {
        $paginationManager = $this->get('mqm_pagination.pagination_manager');
        $categories = $this->get('mqm_category.category_manager')->findAllFamilies($paginationManager);
        $deleteForms = array();
        foreach ($categories as $category) {
            $form = $this->createDeleteForm($category->getId());
            $deleteForms[$category->getId()] = $form->createView();
        }
        
        return array(
            'categories' => $categories,
            'deleteForms' => $deleteForms,
        );      
    }
    
    /**
     * @Route("/administracion/por_categoria/subcategoria/{id}", name="TKShopBackendProductsShowAllSubcategories")
     * @Template("MQMShopBundle:Backend\Product:showAllCategories.html.twig")
     */
    public function showAllSubCategoriesAction($id)
    {
        $category = $this->get('mqm_category.category_manager')->findCategoryBy(array('id' => $id));
        $categories = $category->getCategories();        
        if ($categories == null || count($categories) == 0) {
            return $this->redirect($this->generateUrl('TKShopBackendProductsShowByCategoryAction', array('id' => $category->getId())));
        }
        $deleteForms = array();
        foreach ($categories as $subCategory) {
            $form = $this->createDeleteForm($subCategory->getId());
            $deleteForms[$subCategory->getId()] = $form->createView();
        }
        
        return array(
            'category' => $category,
            'categories' => $categories,
            'deleteForms' => $deleteForms,
        );      
    }
    
    /**
     * @Route("/administracion/por_categoria/{id}", name="TKShopBackendProductsShowByCategoryAction")
     * @Template("MQMShopBundle:Backend\Product:showAll.html.twig")
     */
    public function showByCategoryAction($id)
    {
        $category = $this->get('mqm_category.category_manager')->findCategoryBy(array('id' => $id));
        $products = $category->getProducts();
        $paginationManager = $this->get('mqm_pagination.pagination_manager');
        if ($products != null) {
            $totalItems = count($products);
            $paginationManager->init($totalItems);
            $products = $paginationManager->paginateArray($products);
        }        
        $deleteForms = array();
        foreach ($products as $product) {
            $form = $this->createDeleteForm($product->getId());
            $deleteForms[$product->getId()] = $form->createView();
        }
        
        return array(
            'category' => $category,
            'products' => $products,
            'deleteForms' => $deleteForms,
        );
    }
    
    /**
     * @Route("/recientes.{_format}", defaults={"_format"="partialhtml"}, name="TKShopBackendProductsRecent")
     * @Template()
     */
    public function recentAction($_format)
    {
        $products = $this->get('mqm_product.product_manager')->findRecentProducts();
        
        return array(
            'products' => $products
        );
    }
    
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
}
