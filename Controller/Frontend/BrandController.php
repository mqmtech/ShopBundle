<?php

namespace MQM\ShopBundle\Controller\Frontend;

use MQM\BrandBundle\Entity\Brand;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Exception;

/**
 * Frontend\Default controller.
 *
 * @Route("/tienda/marcas")
 */
class BrandController extends Controller {

    /**
     * @Route("/ver_todo.{_format}", defaults={"_format"="html"}, name="TKShopFrontendBrandsShowAll")
     */
    public function showAllAction($_format) {
        $brands = $this->get('mqm_brand.brand_manager')->findBrands();
        
        return $this->render("MQMShopBundle:Frontend\Brand:showAll.".$_format.".twig", array(
            'brands' => $brands,
            )
        );
    }
    
    /**
     * @Route("/ver_aleatorio.{_format}", defaults={"_format"="partialhtml"}, name="TKShopFrontendBrandsShowRandom")
     */
    public function showRandomAction($_format) {
        
        $brands = $this->get('mqm_brand.brand_manager')->findRandomBrands();

        return $this->render("MQMShopBundle:Frontend\Brand:showAll.".$_format.".twig", array(
            'brands' => $brands)
        );
    }
    
}
