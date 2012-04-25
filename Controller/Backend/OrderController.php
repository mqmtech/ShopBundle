<?php

namespace MQM\ShopBundle\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MQM\ShopBundle\Form\Type\OrderType;

/**
 * @Route("/admin/pedidos")
 */
class OrderController extends Controller
{
    /**
     * @Route("/ver_todos", name="TKShopBackendOrdersShowAll")
     * @Template()
     */
    public function showAllAction()
    {
        $sortManager = $this->createSortManager();   
        $paginationManager = $this->get('mqm_pagination.pagination_manager');
        $paginationManager->setLimitPerPage(20);                     
        $orders = $this->get('mqm_order.order_manager')->findOrders($sortManager, $paginationManager);
        
        $orderForms = array();
        foreach ($orders as $order) {
          $form = $this->createForm(new OrderType(), $order);
          $orderForms [] = $form->createView();
        }        
        
        return array(
            'orderForms' => $orderForms, 
            'sortManager' => $sortManager->switchMode(),
        );      
    }

    /**
     * @Route("/{id}/actualizar", name="TKShopBackendOrderUpdate")
     * @Method("post")
     * @Template("MQMShopBundle:Backend\Order:edit.html.twig")
     */
    public function updateAction($id)
    {
        $orderManager = $this->get('mqm_order.order_manager');
        $order = $orderManager->findOrderBy(array('id' => $id));
        if (!$order) {
            throw $this->createNotFoundException('Unable to find the order');
        }
        $editForm   = $this->createForm(new OrderType(), $order);
        $request = $this->getRequest();
        $editForm->bindRequest($request);
        if ($editForm->isValid()) {
            $orderManager->saveOrder($order);

            return $this->redirect($this->generateUrl('TKShopBackendOrdersShowAll'));
        }
        
        return array(
            'order'  => $order,
            'form'   => $editForm->createView(),
        );
    }
    
    private function createSortManager()
    {
        $sortManager = $this->get('mqm_sort.sort_manager');
        $sortManager->addSort('pedido', 'publicId', 'pedido')
                    ->addSort('fecha', 'createdAt', 'Fecha', 'ASC', array('default' => true))
                    ->addSort('cantidad', 'quantity', 'Cantidad')
                    ->addSort('importe', 'totalPrice', 'Importe')
                    ->addSort('estado', 'status', 'Estado')
                    ->init();
        
        return $sortManager;
    }
}
