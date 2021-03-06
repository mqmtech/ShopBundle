<?php

namespace MQM\ShopBundle\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MQM\CategoryBundle\Entity\Category;

/**
 * @Route("/admin/categorias")
 */
class CategoryController extends Controller
{
    
    /**
     * @Route("/", name="TKShopBackendCategoriesIndex")
     * @Template()
     */
    public function indexAction()
    {
        $entities = $this->getCategoryManager()->findCategories();
        return array('entities' => $entities);
    }

    /**
     * @Route("/{id}/ver", name="TKShopBackendCategoryShow")
     * @Template()
     */
    public function showAction($id)
    {
        $entity = $this->getCategoryManager()->findCategoryBy(array('id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Shop\Category entity.');
        }
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        );
    }

    /**
     * @Route("/~/nuevo", name="TKShopBackendCategoryNew")
     * @Template()
     */
    public function newAction()
    {
        $entity = $this->getCategoryManager()->createCategory();        
        $categoryType = $this->get('mqm_shop.form.category');
        $form = $this->createForm($categoryType, $entity);
        
        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * @Route("/create", name="TKShopBackendCategoryCreate")
     * @Method("post")
     * @Template("MQMShopBundle:Backend\Category:new.html.twig")
     */
    public function createAction()
    {
        $categoryManager = $this->getCategoryManager();
        $entity  = $categoryManager->createCategory();
        $request = $this->getRequest();
        $categoryType = $this->get('mqm_shop.form.category');
        $form    = $this->createForm($categoryType, $entity);
        $form->bindRequest($request);
        if ($form->isValid()) {            
            $categoryManager->saveCategory($entity);
            
            return $this->redirect($this->generateUrl('TKShopBackendCategoriesShowAll'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * @Route("/{id}/editar", name="TKShopBackendCategoryEdit")
     * @Template()
     */
    public function editAction($id)
    {
        $entity = $this->getCategoryManager()->findCategoryBy(array('id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Shop\Category entity.');
        }
        $categoryType = $this->get('mqm_shop.form.category');
        $form = $this->createForm($categoryType, $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'form'   => $form->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    
    /**
     * @Route("/{id}/clonar", name="TKShopBackendCategoryClone")
     * @Template("MQMShopBundle:Backend\Category:new.html.twig")
     */
    public function cloneAction($id)
    {
        $entity = $this->getCategoryManager()->findCategoryBy(array('id' => $id));        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Shop\Category entity.');
        }        
        $entityCloned = clone ($entity);
        $categoryType = $this->get('mqm_shop.form.category');
        $form = $this->createForm($categoryType, $entityCloned);

        return array(
            'entity'      => $entityCloned,
            'form'   => $form->createView(),
        );
        
    }

    /**
     * @Route("/{id}/actualizar", name="TKShopBackendCategoryUpdate")
     * @Method("post")
     * @Template("MQMShopBundle:Backend\Category:edit.html.twig")
     */
    public function updateAction($id)
    {
        $categoryManager = $this->getCategoryManager();
        $entity = $categoryManager->findCategoryBy(array('id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Shop\Category entity.');
        }
        $categoryType = $this->get('mqm_shop.form.category');
        $form   = $this->createForm($categoryType, $entity);
        $deleteForm = $this->createDeleteForm($id);
        $request = $this->getRequest();
        $form->bindRequest($request);
        if ($form->isValid()) {            
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
            else{
                throw new \Exception("Custom Exception: Image is null in the updateAction");
            }
            //END FIX VIRTUAL file update in Image 
            $categoryManager->saveCategory($entity);

            return $this->redirect($this->generateUrl('TKShopBackendCategoriesShowAll'));
        }

        return array(
            'entity'      => $entity,
            'form'   => $form->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    
     /**
     *
     * @Route("/administracion", name="TKShopBackendCategoriesShowAll")
     * @Template()
     */
    public function showAllAction() {
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
     *
     * @Route("/administracion/subcategoria/{id}", name="TKShopBackendCategoriesShowAllSubcategories")
     * @Template("MQMShopBundle:Backend\Category:showAll.html.twig")
     */
    public function showAllSubCategoriesAction($id) 
    {
        $category = $this->getCategoryManager()->findCategoryBy(array('id' => $id));
        $categories = $category->getCategories();        
        $paginationManager = $this->get('mqm_pagination.pagination_manager');
        if($categories != null){
            $totalItems = count($categories);
            $paginationManager->init($totalItems); 
            $categories = $paginationManager->paginateArray($categories);
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
     * @Route("/{id}/delete", name="TKShopBackendCategoryDelete")
     * @Method("post")
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();
        $form->bindRequest($request);
        if ($form->isValid()) {
            $entity = $this->get('mqm_category.category_manager')->findCategoryBy(array('id' => $id));
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Shop\Category entity.');
            }            
            try {
                $this->get('mqm_category.category_manager')->deleteCategory($entity);
            }
            catch(\Exception $e) {
                 $this->get('session')->getFlashBag()->set('category_error',"Atencion: La categoria no puede ser eliminada, elimine las subcategorias y productos asociados previamente");
                 $this->get('session')->save();
            }
        }

        return $this->redirect($this->generateUrl('TKShopBackendCategoriesShowAll'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
    private function getCategoryManager()
    {
        return $this->get('mqm_category.category_manager');
    }
}
