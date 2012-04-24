<?php

namespace MQM\ShopBundle\Controller\Frontend;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/tienda/busqueda")
 */
class SearchController extends Controller
{    
    /**
     * @Route("/productos/por_marca/{id}", name="TKShopFrontendSearchProductsByBrand")
     */
    public function searchProductByBrand($id)
    {
        $sortManager = $this->createSortManager();        
        $brand = $this->get('mqm_brand.brand_manager')->findBrandBy(array('id' => $id));
        $paginationManager = $this->get('mqm_pagination.pagination_manager');
        $products = $this->get('mqm_product.product_manager')->findProductsByBrandId($id, $sortManager, $paginationManager);
        $productsPrice = null;
        if($products != null){
            $productsPrice = $this->get('mqm_pricing.pricing_manager')->getProductsPrice($products);
        }
        
        return $this->render('MQMShopBundle:Frontend/Search:products.html.twig', 
                array('products' => $products,
                    'productsPrice' => $productsPrice,
                    'sortManager' => $sortManager->switchMode(),
                    'search' => array('name' => $brand->getName()),
                    ));
        
    }    
    
    /**
     * @Route("/productos/por_multi_filtro/", name="TKShopFrontendSearchProductsByMultiQuery")
     */
    public function searchProductByMultiQuery()
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
        $name = $query->get('name');        
        $sortManager = $this->createSortManager();
        $paginationManager = $this->get('mqm_pagination.pagination_manager');        
        $products = $this->get('mqm_product.product_manager')->findProductsByMultiField($name, 'OR', $sortManager, $paginationManager);
        $productsPrice = array();
        if ($products != null) {
            $productsPrice = $this->get('mqm_pricing.pricing_manager')->getProductsPrice($products);
        }

        return $this->render('MQMShopBundle:Frontend/Search:products.html.twig', 
                array('products' => $products, 
                      'productsPrice' => $productsPrice,
                      'search' => array('name' => $name),
                      'sortManager' => $sortManager->switchMode(),
            ));
    }
    
    private function createSortManager()
    {
        $sortManager = $this->get('mqm_sort.sort_manager');
        $sortManager->addSort('name', 'name', 'Producto')
                    ->addSort('price', 'basePrice', 'Precio')
                    ->init();
        
        return $sortManager;
    }
}
