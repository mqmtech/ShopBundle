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
     * @Route("/", name="TKShopBackendStaffUserClientIndex")
     * @Template()
     */
    public function indexAction()
    {
        return array('name' => "Usuario!");
    }    
    
    /**
     * @Route("/recientes.{_format}", defaults={"_format"="partialhtml"}, name="TKShopBackendStaffUserClientRecent")
     * @Template()
     */
    public function recentClientsAction($_format)
    {
        $clients = $this->get('mqm_user.user_manager')->findRecentUsers();        
        return $this->render("MQMShopBundle:Backend\User\Client:recentClients.".$_format.".twig", array('clients' => $clients));
    }
    
    /**
     * @Route("/ver_todos.{_format}", defaults={"_format"="html"}, name="TKShopBackendStaffUserClientShowAll")
     * @Template()
     */
    public function showAllAction($_format)
    {
        $users = $this->get('mqm_user.user_manager')->findStaffUsers();
        $paginationManager = $this->get('mqm_pagination.pagination_manager');
        if($users != null){
            $totalItems = count($users);
            $paginationManager->init($totalItems); 
            $users = $paginationManager->paginateArray($users);
        } 
        $sortManager = $this->createSortManager();

        return $this->render("MQMShopBundle:Backend\User\Staff:showAll.html.twig",array(
            'users' => $users,
            'sortManager' => $sortManager,
        ));
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

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm();
    }
}
