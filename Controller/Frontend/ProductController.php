<?php

namespace MQM\ShopBundle\Controller\Frontend;
use MQM\ProductBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Exception;

/**
 * @Route("/tienda/productos")
 */
class ProductController extends Controller
{
    /**
     * @Route("/", name="TKShopFrontendProductIndex")
     * @Template()
     */
    public function indexAction()
    {
        //return $this->redirect($this->generateUrl("TKShopFrontendProductShow"));
        return array();
    }
    
    /**
     * @Route("/por_categoria/{categoryId}.{_format}", defaults={"_format"="html"}, name="TKShopFrontendProductsShowByCategory")
     * @Template()
     */
    public function showByCategoryAction($categoryId)
    {
        $sortManager = $this->get('mqm_sort.sort_manager');
        $sortManager->addSort('name', 'name', 'Producto')
                    ->addSort('price', 'basePrice', 'Precio')
                    ->init();
        
        $category = $this->get('mqm_category.category_manager')->findCategoryBy(array('id' => $categoryId));
        if ($category == null) {
            throw new Exception("Custom Exception: Category not found");
        }
        $rootCategory = $category->getRootCategory();
        
        $paginationManager = $this->get('mqm_pagination.pagination_manager');
        $products = $this->get('mqm_product.product_manager')->findProductsByCategoryId($categoryId, $sortManager, $paginationManager);
        $productsPrice = null;
        if ($products != null) {
            $productsPrice = $this->get('mqm_pricing.pricing_manager')->getProductsPrice($products);
        }
        
        return array(
            'rootCategory' => $rootCategory,
            'category' => $category,
            'products' => $products,
            'productsPrice' => $productsPrice,
            'sortManager' => $sortManager->switchMode(),
        );
    }
    
    /**
     *
     * @param integer $productId
     * @return HttpResponse
     * @Route("/{productId}/productos_relacionados/", defaults={"_format" = "partialhtml"}, name = "TKShopBundleFrontendProductRelatedProducts") 
     * @Template()
     */
    public function relatedProductsAction($productId)
    {
        $relatedProducts = $this->get('mqm_product.product_manager')->findRelatedProductsByProductId($productId);
        $relatedProductsPrice = $this->get('mqm_pricing.pricing_manager')->getProductsPrice($relatedProducts);
        
        return array(
            'products' => $relatedProducts,
            'productsPrice' => $relatedProductsPrice
        );
    }
    
     /**
     * Frontend Product
     *
     * @Route("/{productId}/ver", name="TKShopFrontendProductShow")
     * @Template()
     */
    public function showAction($productId)
    {
        $product = $this->get('mqm_product.product_manager')->findProductBy(array('id' => $productId));        
        if ($product == null) {
           throw $this->createNotFoundException("Custom Exception: Product Not Found");
        }        
        $relatedCategory = $product->getCategory();
        
        $breadcrumb = array();
        $i=0; $maxBreadcrumb = 6;
        while ($i < $maxBreadcrumb) {
            if ($relatedCategory != null) {
                array_unshift($breadcrumb, $relatedCategory);  
                $relatedCategory = $relatedCategory->getParentCategory();
            }
            else{
                break;
            }
            $i++;
        }        
        $productPrice = $this->get('mqm_pricing.pricing_manager')->getProductPrice($product);
        $this->registerProductViewStatistic($product);
        
        return array(
            'breadcrumb' => $breadcrumb,
            'product' => $product,
            'productPrice' => $productPrice
        );
    }

    public function registerProductViewStatistic(\MQM\ProductBundle\Model\ProductInterface $product)
    {
        $statisticManager = $this->get('mqm_statistic.statistic_manager');
        $productDimension = $statisticManager->findOneBy(array('sku' => $product->getSku()), 'product');
        if ($productDimension == null) {
            $productDimension = $statisticManager->createDimension('product');
            $productDimension->setName($product->getName());
            $productDimension->setSku($product->getSku());
            $productDimension->setCategoryName($product->getcategory()->getName());
            $productDimension->setBrandName($product->getBrand()->getName());
        }
        $productViewStatistic = $statisticManager->createStatistic('productViewStatistic');
        $productViewStatistic->setProduct($productDimension);
        $statisticManager->save($productViewStatistic, true);
    }
}
