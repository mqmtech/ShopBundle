<?php

namespace MQM\ShopBundle\Controller\Frontend;

use MQM\BrandBundle\Entity\Brand;
use MQM\ProductBundle\Entity\Product;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Exception;

/**
 * @Route("/tienda/categorias")
 */
class CategoryController extends Controller {

    /**
     * @Route("/", name="TKShopFrontendCategoryIndex")
     * @Template()
     */
    public function indexAction() 
    {
        $categories = $this->get('mqm_category.category_manager')->findAllFamilies();
        
        return array(
            'categories' => $categories,
        );
    }
    
    /**
     * @Route("/ver_familias_y_categorias.{_format}", defaults={"_format"="partialhtml"}, name="TKShopFrontendCategoriesShowAll")
     * @Template()
     */
    public function showAllAction() 
    {
        $categories = $this->get('mqm_category.category_manager')->findCategories();
        
        return array(
            'categories' => $categories
        );
    }
    
    /**
     * @Route("/ver_familias.{_format}", defaults={"_format"="partialhtml"}, name="TKShopFrontendCategoriesShowAllFamilies")
     */
    public function showAllFamiliesAction($_format)
    {
        $categories = $this->get('mqm_category.category_manager')->findAllFamilies();

        return $this->render("MQMShopBundle:Frontend\Category:showAllFamilies.".$_format.".twig", array(
            'categories' => $categories)
        );
    }
    
    /**
     * @Route("/{id}/ver_categorias.{_format}", defaults={"_format"="html"}, name="TKShopFrontendCategoriesShowAllCategories")
     */
    public function showAllCategoriesAction($id, $_format)
    {
        $category = $this->get('mqm_category.category_manager')->findCategoryBy(array('id' => $id));
        if ($category != null) {
            $categories = $category->getCategories();
        }
        $totalCats = count($categories);
        if($categories != null && $totalCats > 0 || $category->getParentCategory() == null){
            $paginationManager = $this->get('mqm_pagination.pagination_manager');
            $paginationManager->init($totalCats);
            $categories = $paginationManager->paginateArray($categories);

            return $this->render("MQMShopBundle:Frontend\Category:showAllCategories.".$_format.".twig", array(
                'categories' => $categories,
                'parentCategory' => $category,
                )
            );
        }
        else {           
            return $this->redirect($this->generateUrl('TKShopFrontendProductsShowByCategory', array(
            'categoryId' => $id
            )));
        }
    }
}
