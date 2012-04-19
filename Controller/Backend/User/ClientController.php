<?php

namespace MQM\ShopBundle\Controller\Backend\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Exception;

/**
 * Frontend\Default controller.
 *
 * @Route("/admin/usuarios/clientes")
 */
class ClientController extends Controller {
    
    /**
     * Backend Productos
     *
     * @Route("/", name="TKShopBackendUserClientIndex")
     * @Template()
     */
    public function indexAction() {
        return array('name' => "Usuario!");
    }
    
    
    /**
     * @Route("/recientes.{_format}", defaults={"_format"="partialhtml"}, name="TKShopBackendUserClientRecent")
     * @Template()
     */
    public function recentClientsAction($_format){
        $clients = $this->get('mqm_user.user_manager')->findRecentUsers();        
        return $this->render("MQMShopBundle:Backend\User\Client:recentClients.".$_format.".twig", array('clients' => $clients));
    }

}
