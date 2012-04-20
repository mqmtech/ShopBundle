<?php

namespace MQM\ShopBundle\Controller\Frontend;

use MQM\BrandBundle\Entity\Brand;
use MQM\ProductBundle\Entity\Product;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MQM\CategoryBundle\Entity\Category;
use MQM\ShopBundle\Form\Type\CategoryType;
use Doctrine\ORM\EntityManager;
use Exception;
use MQM\ShoppingCartBundle\Entity\ShoppingCart;

/**
 * Frontend\Default controller.
 *
 * @Route("/tienda")
 */
class DefaultController extends Controller {

    /**
     * Frontend demo
     *
     * @Route("/", name="TKShopFrontendIndex")
     * @Template()
     */
    public function indexAction() {
        $categories = $this->get('mqm_category.category_manager')->findRandomFamilies();
        
        return array(
            'entities' => $categories,
        );
        
    }
    
     /**
     * Return static pages
     * @Route("/empresa", name="TKShopFrontendCompanyShow")
     * @param type $page
     * @return type 
     */
    public function companyAction()
    {
        return $this->render("MQMShopBundle:Default:empresa.html.twig");
    }
    
    /**
     * Return static pages
     * @Route("/politica", name="TKShopFrontendPolicyShow")
     * @param type $page
     * @return type 
     */
    public function policyAction()
    {
        return $this->render("MQMShopBundle:Default:politica.html.twig");
    }
    
    /**
     * Frontend demo
     *
     * @Route("/index2", name="TKShopFrontendIndex2")
     * @Template()
     */
    public function index2Action() {
        $blog = (object) array(
            'title' => 'Test',
            'body' => 'Main body'            
        );
        
        $container = array(
            'blog' => $blog
        );
        return array('blog_entries' => $container);
    }

}
