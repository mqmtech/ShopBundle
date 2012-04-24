<?php

namespace MQM\ShopBundle\Controller\Backend;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 *
 * @Route("/admin/busqueda")
 */
class SearchController extends Controller
{
    /**
     * @Route("/productos/por_multi_filtro/", name="TKShopBackendSearchProductsByMultiQuery")
     */
    public function searchProductByMultiQueryAction()
    {        
        $request = Request::createFromGlobals();
        $method = $request->getMethod();
        $query = null;        
        if ($method == 'POST') {
            $query = $request->request;
        }
        else {
            $query = $request->query;
        }
        $name = $request->query->get('name');
       
        $paginationManager = $this->get('mqm_pagination.pagination_manager');
        $products = $this->get('mqm_product.product_manager')->findProductsByMultiField($name, 'OR', null, $paginationManager);     
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
