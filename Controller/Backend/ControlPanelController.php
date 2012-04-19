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
 * Frontend\Default controller.
 *
 * @Route("/admin/panelControl")
 */
class ControlPanelController extends Controller {
    /**
     * Backend Productos
     *
     * @Route("/", name="TKShopBackendPanelControlIndex")
     * @Template()
     */
    public function indexAction() {
        return array('name' => "ControlPanel!");
         
    }
}
