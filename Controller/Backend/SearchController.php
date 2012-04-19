<?php

namespace MQM\ShopBundle\Controller\Backend;

use Symfony\Component\HttpFoundation\Request;
use MQM\ProductBundle\Entity\Product;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Exception;

/**
 *
 * @Route("/admin/busqueda")
 */
class SearchController extends Controller {

    /**
     * TODO: Change it to POST for security!
     * @Route("/productos/por_multi_filtro/", name="TKShopBackendSearchProductsByMultiQuery")
     */
    public function searchProductByMultiQueryAction()
    {        
        $request = Request::createFromGlobals();
        $method = $request->getMethod();
        $query = null;        
        if($method == 'POST'){
            $query = $request->request;
        }
        else{
            //TODO: Send Error msg
            $query = $request->query;
        }
        $name = $request->query->get('name');
       
        $pagination = $this->get('mqm_pagination.pagination_manager');
        $products = $this->get('mqm_product.product_manager')->findProductsByMultiField($name, 'OR', null, $pagination);     
        $deleteForms = $this->createProductsDeletionForms($products);

        return $this->render('MQMShopBundle:Backend/Search:products.html.twig', 
                array('products' => $products, 
                      'deleteForms' => $deleteForms,
                      'search' => array('name' => $name)
            ));
    }
    
    private function createProductsDeletionForms($products)
    {
        $deleteForms = array();
        foreach ($products as $product) {
            $form = $this->createDeleteForm($product->getId());
            $deleteForms[$product->getId()] = $form->createView();
        }

        return $deleteForms;
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
