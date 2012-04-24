<?php

namespace MQM\ShopBundle\Controller\Backend\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/admin/usuarios/empleados")
 */
class StaffController extends Controller
{    
    /**
     * @Route("/", name="TKShopBackendUserStaffIndex")
     * @Template()
     */
    public function indexAction()
    {
        return array('name' => "Usuario!");
    }
}
