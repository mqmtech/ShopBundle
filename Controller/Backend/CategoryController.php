<?php

namespace MQM\ShopBundle\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MQM\CategoryBundle\Entity\Category;
use MQM\ShopBundle\Form\Shop\CategoryType;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Shop\Category controller.
 *
 * @Route("/admin/categorias")
 */
class CategoryController extends Controller
{
    
    /**
     * Lists all Shop\Category entities.
     *
     * @Route("/", name="TKShopBackendCategoriesIndex")
     * @Template()
     */
    public function indexAction()
    {
        $entities = $this->get('mqm_category.category_manager')->findCategories();
        return array('entities' => $entities);
    }

    /**
     * Finds and displays a Shop\Category entity.
     *
     * @Route("/{id}/ver", name="TKShopBackendCategoryShow")
     * @Template()
     */
    public function showAction($id)
    {
        $entity = $this->get('mqm_category.category_manager')->findCategoryBy(array('id' => $id));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Shop\Category entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        );
    }

    /**
     * Displays a form to create a new Shop\Category entity.
     *
     * @Route("/~/nuevo", name="TKShopBackendCategoryNew")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Category();
        
        $categoryType = $this->get('mqm_shop.form.category');
        $form = $this->createForm($categoryType, $entity);
        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Creates a new Shop\Category entity.
     *
     * @Route("/create", name="TKShopBackendCategoryCreate")
     * @Method("post")
     * @Template("MQMShopBundle:Backend\Category:new.html.twig")
     */
    public function createAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        $entity  = new Category();
        $request = $this->getRequest();
        $categoryType = $this->get('mqm_shop.form.category');
        $form    = $this->createForm($categoryType, $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('TKShopBackendCategoriesShowAll'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing Shop\Category entity.
     *
     * @Route("/{id}/editar", name="TKShopBackendCategoryEdit")
     * @Template()
     */
    public function editAction($id)
    {
        $entity = $this->get('mqm_category.category_manager')->findCategoryBy(array('id' => $id));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Shop\Category entity.');
        }

        $categoryType = $this->get('mqm_shop.form.category');
        $editForm = $this->createForm($categoryType, $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    
    /**
     * Displays a form to edit an existing Shop\Category entity.
     *
     * @Route("/{id}/clonar", name="TKShopBackendCategoryClone")
     * @Template("MQMShopBundle:Backend\Category:new.html.twig")
     */
    public function cloneAction($id)
    {
        $entity = $this->get('mqm_category.category_manager')->findCategoryBy(array('id' => $id));
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Shop\Category entity.');
        }
        
        $entityCloned = clone ($entity);

        $categoryType = $this->get('mqm_shop.form.category');
        $editForm = $this->createForm($categoryType, $entityCloned);

        return array(
            'entity'      => $entityCloned,
            'form'   => $editForm->createView(),
        );
        
    }

    /**
     * Edits an existing Shop\Category entity.
     *
     * @Route("/{id}/actualizar", name="TKShopBackendCategoryUpdate")
     * @Method("post")
     * @Template("MQMShopBundle:Backend\Category:edit.html.twig")
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $this->get('mqm_category.category_manager')->findCategoryBy(array('id' => $id));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Shop\Category entity.');
        }

        $categoryType = $this->get('mqm_shop.form.category');
        $editForm   = $this->createForm($categoryType, $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            
            //FIX VIRTUAL file update in Image
            $image = $entity->getImage();
            if($image != null){
                if($image->isFileUpdated() == false){
                    $image->setFileUpdated(true);
                }
                else{
                    $image->setFileUpdated(false);
                }   
            }
            else{
                throw new \Exception("Custom Exception: Image is null in the updateAction");
            }
            //END FIX VIRTUAL file update in Image 
            
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('TKShopBackendCategoriesShowAll'));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    
     /**
     *
     * @Route("/administracion", name="TKShopBackendCategoriesShowAll")
     * @Template()
     */
    public function showAllAction() {
        $pagination = $this->get('mqm_pagination.pagination_manager'); 
        $categories = $this->get('mqm_category.category_manager')->findAllFamilies($pagination);
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
        $category = $this->get('mqm_category.category_manager')->findCategoryBy(array('id' => $id));
        $categories = $category->getCategories();        
        $pagination = $this->get('mqm_pagination.pagination_manager');
        if($categories != null){
            $totalItems = count($categories);
            $pagination->paginate($totalItems); 
            $categories = $pagination->getPaginatedElements($categories);
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
     * Deletes a Shop\Category entity.
     *
     * @Route("/{id}/delete", name="TKShopBackendCategoryDelete")
     * @Method("post")
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $this->get('mqm_category.category_manager')->findCategoryBy(array('id' => $id));

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Shop\Category entity.');
            }
            
            try {
                $em->remove($entity);
                $em->flush();
            }
            catch(\Exception $e){
                 $this->get('session')->setFlash('category_error',"Atencion: La categoria no puede ser eliminada, elimine las subcategorias y productos asociados previamente");
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
}
