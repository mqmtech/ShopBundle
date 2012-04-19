<?php

namespace MQM\ShopBundle\Controller\Backend;

use MQM\BrandBundle\Entity\Brand;

use MQM\ProductBundle\Entity\Product;

use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Validator\Constraints\Image;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Exception;

/**
 * Backend\Default controller.
 *
 * @Route("/admin")
 */
class DefaultController extends Controller {
	
	
	/**
	 * Backend Demo
	 *
	 * @Route("/", name="TKShopBackendIndex")
	 * @Template()
	 */
	public function indexAction() {
		//return array ('name' => "We're in Backend" );
                return $this->redirect($this->generateUrl('TKShopBackendPanelControlIndex'));
	}
        
        

}
