<?php

namespace MQM\ShopBundle\Controller\Backend\Discount;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/admin/descuentos")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/tipo_de_descuentos", name="TKShopBackendDiscountIndex")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
}
