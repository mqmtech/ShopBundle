<?php

namespace MQM\ShopBundle\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Backend\Default controller.
 *
 * @Route("/admin")
 */
class DefaultController extends Controller
{	
    /**
        * @Route("/", name="TKShopBackendIndex")
        * @Template()
        */
    public function indexAction()
    {
        return $this->redirect($this->generateUrl('TKShopBackendPanelControlIndex'));
    }
}
