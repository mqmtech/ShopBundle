<?php

namespace MQM\ShopBundle\Controller\Backend\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use MQM\UserBundle\Model\UserManagerInterface;
use MQM\UserBundle\Model\UserInterface;

/**
 * @Route("/admin/usuarios/clientes")
 */
class ClientController extends Controller
{    
    /**
     * @Route("/", name="TKShopBackendUserClientIndex")
     * @Template()
     */
    public function indexAction()
    {
        $clients = $this->get('mqm_user.user_manager')->findUsersBy(array(
            'isEnabled' => false,
            'permissionType' => 'ROLE_USER'
        ));
        
        $paginationManager = $this->get('mqm_pagination.pagination_manager');
        if($clients != null){
            $totalItems = count($clients);
            $paginationManager->init($totalItems); 
            $clients = $paginationManager->paginateArray($clients);
        }        
        $sortManager = $this->createSortManager();

        return array(
            'clients' => $clients,
            'sortManager' => $sortManager,
        );
    }
    
    /**
     * @Route("/recientes.{_format}", defaults={"_format"="partialhtml"}, name="TKShopBackendUserClientRecent")
     * @Template()
     */
    public function recentClientsAction($_format)
    {
        $clients = $this->get('mqm_user.user_manager')->findRecentUsers();        
        return $this->render("MQMShopBundle:Backend\User\Client:recentClients.".$_format.".twig", array('clients' => $clients));
    }
    
    /**
     * @Route("/ver_todos.{_format}", defaults={"_format"="html"}, name="TKShopBackendUserClientShowAll")
     * @Template()
     */
    public function showAllAction($_format)
    {
        $validatedClients = $this->get('mqm_user.user_manager')->findUsersBy(array(
            'isEnabled' => true,
            'permissionType' => 'ROLE_USER'
        ));
        $paginationManager = $this->get('mqm_pagination.pagination_manager');
        if($validatedClients != null){
            $totalItems = count($validatedClients);
            $paginationManager->init($totalItems); 
            $validatedClients = $paginationManager->paginateArray($validatedClients);
        }
        $sortManager = $this->createSortManager();

        return $this->render("MQMShopBundle:Backend\User\Client:showAll.html.twig",array(
            'validatedClients' => $validatedClients,
            'sortManager' => $sortManager,
        ));
    }

    /**
     * @Route("/{id}/perfil.{_format}", defaults={"_format"="html"}, name="TKShopBackendUserProfile")
     * @Template()
     */
    public function profileAction($id)
    {
        $user = $this->getUserManager()->findUserBy(array(
                'id' => $id
            )
        );
        if ($user == null) {
            throw $this->createNotFoundException('No user was found');
        }

        return array(
            'user' => $user
        );
    }

    /**
     * @return UserManagerInterface
     */
    private function getUserManager()
    {
        return $this->get('mqm_user.user_manager');
    }

    private function createSortManager()
    {
        $sortManager = $this->get('mqm_sort.sort_manager');
        $sortManager->addSort('nombre', 'username', 'Nombre')
            ->addSort('fecha', 'createdAt', 'Fecha', 'ASC', array('default' => true))
            ->addSort('estado', array('isEnabled'), 'Estado')
            ->init();

        return $sortManager;
    }
}
