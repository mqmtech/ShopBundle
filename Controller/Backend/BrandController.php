<?php

namespace MQM\ShopBundle\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MQM\BrandBundle\Entity\Brand;
use MQM\ShopBundle\Form\Type\BrandType;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Shop\Brand controller.
 *
 * @Route("/admin/marcas")
 */
class BrandController extends Controller
{
    /**
     * Lists all Shop\Brand entities.
     *
     * @Route("/", name="TKShopBackendBrandsIndex")
     * @Template()
     */
    public function indexAction()
    {
        $entities = $this->get('mqm_brand.brand_manager')->findBrands();

        return array('entities' => $entities);
    }

    /**
     * Finds and displays a Shop\Brand entity.
     *
     * @Route("/{id}/ver", name="TKShopBackendBrandShow")
     * @Template()
     */
    public function showAction($id)
    {
        $entity = $this->get('mqm_brand.brand_manager')->findBrandBy(array('id' => $id));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Shop\Brand entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        );
    }
    
    /**
     *
     * @Route("/administracion", name="TKShopBackendBrandsShowAll")
     * @Template()
     */
    public function showAllAction()
    {
        $paginationManager = $this->get('mqm_pagination.pagination_manager');
        $brands = $this->get('mqm_brand.brand_manager')->findBrands($paginationManager); 
        $deleteForms = array();
        foreach ($brands as $brand) {
            $form = $this->createDeleteForm($brand->getId());
            $deleteForms[$brand->getId()] = $form->createView();
        }
        
        return array(
            'brands' => $brands,
            'deleteForms' => $deleteForms,
        );      
    }

    /**
     * Displays a form to create a new Shop\Brand entity.
     *
     * @Route("/~/nuevo", name="TKShopBackendBrandNew")
     * @Template()
     */
    public function newAction()
    {
        $entity = $this->get('mqm_brand.brand_manager')->createBrand();
        $form   = $this->createForm(new BrandType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Creates a new Shop\Brand entity.
     *
     * @Route("/create", name="TKShopBackendBrandCreate")
     * @Method("post")
     * @Template("MQMShopBundle:Backend\Brand:new.html.twig")
     */
    public function createAction()
    {
        $entity = $this->get('mqm_brand.brand_manager')->createBrand();
        $request = $this->getRequest();
        $form    = $this->createForm(new BrandType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $this->get('mqm_brand.brand_manager')->saveBrand($entity);

            return $this->redirect($this->generateUrl('TKShopBackendBrandsShowAll'));            
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing Shop\Brand entity.
     *
     * @Route("/{id}/editar", name="TKShopBackendBrandEdit")
     * @Template()
     */
    public function editAction($id)
    {
        $entity = $this->get('mqm_brand.brand_manager')->findBrandBy(array('id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Shop\Brand entity.');
        }

        $editForm = $this->createForm(new BrandType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Shop\Brand entity.
     *
     * @Route("/{id}/actualizar", name="TKShopBackendBrandUpdate")
     * @Method("post")
     * @Template("MQMShopBundle:Backend\Brand:edit.html.twig")
     */
    public function updateAction($id)
    {
        $brandManager = $this->get('mqm_brand.brand_manager');
        $entity = $brandManager->findBrandBy(array('id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Shop\Brand entity.');
        }

        $editForm   = $this->createForm(new BrandType(), $entity);
        $deleteForm = $this->createDeleteForm($id);
        $request = $this->getRequest();
        $editForm->bindRequest($request);
        if ($editForm->isValid()) {
            //FIX VIRTUAL file update in Image
            $image = $entity->getImage();
            if ($image != null) {
                if ($image->isFileUpdated() == false) {
                    $image->setFileUpdated(true);
                }
                else{
                    $image->setFileUpdated(false);
                }   
            }
            //END FIX VIRTUAL file update in Image 
            $brandManager->saveBrand($entity);

            return $this->redirect($this->generateUrl('TKShopBackendBrandsShowAll'));
        }
        
        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    
        /**
     * Displays a form to edit an existing Shop\Brand entity.
     *
     * @Route("/{id}/clonar", name="TKShopBackendBrandClone")
     * @Template("MQMShopBundle:Backend\Brand:new.html.twig")
     */
    public function cloneAction($id)
    {
        $entity = $this->get('mqm_brand.brand_manager')->findBrandBy(array('id' => $id));        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Shop\Brand entity.');
        }        
        $entityCloned = clone ($entity);
        $editForm = $this->createForm(new BrandType(), $entityCloned);
        
        return array(
            'entity'      => $entityCloned,
            'form'   => $editForm->createView(),
        );
    }

    /**
     * Deletes a Shop\Brand entity.
     *
     * @Route("/{id}/delete", name="TKShopBackendBrandDelete")
     * @Method("post")
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();
        $form->bindRequest($request);
        if ($form->isValid()) {
            $brandManager = $this->get('mqm_brand.brand_manager');
            $entity = $brandManager->findBrandBy(array('id' => $id));
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Shop\Brand entity.');
            }
            try {
                $brandManager->remove($entity);
            }
            catch(\Exception $e) {
                $this->get('session')->setFlash('category_error',"Atencion: La MARCA no puede ser eliminada, eliminela de los productos previamente");
            }
        }

        return $this->redirect($this->generateUrl('TKShopBackendBrandsShowAll'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
