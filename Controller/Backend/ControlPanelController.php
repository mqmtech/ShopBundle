<?php

namespace MQM\ShopBundle\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Frontend\Default controller.
 *
 * @Route("/admin/panelControl")
 */
class ControlPanelController extends Controller
{
    /**
     * Backend Productos
     *
     * @Route("/", name="TKShopBackendPanelControlIndex")
     * @Template()
     */
    public function indexAction()
    {
        return array('name' => "ControlPanel!");
         
    }
}
