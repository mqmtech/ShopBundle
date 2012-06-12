<?php

namespace MQM\ShopBundle\Controller\Frontend\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MQM\OrderBundle\Model\OrderInterface;

/**
 * @Route("/tienda/usuario/pedidos")
 */
class OrderController extends Controller
{
     /**
     * @Route("/realizados", name="TKShopFrontendOrdersShowDelivered")
     * @Template()
     */
    public function showDeliveredAction()
    {
        $sortManager = $this->createSortManager();
        $user = $this->getCurrentUser();
        $entities = $this->get('mqm_order.order_manager')->findDeliveredOrdersByUserId($user->getId(), $sortManager);

        return array(
            'entities' => $entities,
            'sortManager' => $sortManager->switchMode(),
        );
    }
    
    /**
     * @Route("/en_proceso", name="TKShopFrontendOrdersShowInProcess")
     * @Template()
     */
    public function showInProcessAction()
    {
        $sortManager = $this->createSortManager();        
        $user = $this->getCurrentUser();
        $entities = $this->get('mqm_order.order_manager')->findInProcessOrdersByUserId($user->getId(), $sortManager);

        return array(
            'entities' => $entities,
            'sortManager' => $sortManager->switchMode(),
        );
    }

    /**
     * @Route("/{publicId}/show", name="TKShopFrontendOrderShow")
     * @Template()
     */
    public function showAction($publicId)
    {
        $order = $this->get('mqm_order.order_manager')->findOrderByPublicId($publicId);
        if (!$order) {
            throw $this->createNotFoundException('Unable to find Shop\Order entity.');
        }

        return array(
            'order'      => $order,
        );
    }
    
    /**
     * @Route("/confirmar", name="TKShopFrontendOrderCreateFromShoppingCart")
     * @Template()
     */
    public function createFromShoppingCartAction()
    {
        $confirmed = $this->placeOrder();        
        if ($confirmed == true) {
            return $this->redirect($this->generateUrl('TKShopFrontendOrdersShowInProcess'));
        }
        
        else{
            return $this->redirect($this->generateUrl('TKShopFrontendUserShoppingCartEdit'));
        }
    }
    
    private function placeOrder()
    {        
        $sc = $this->getUserShoppingCart();
        $items = $sc->getItems();
        if (count($items) < 1) {
            return false;
        }
        try {
            $checkoutManager = $this->get('mqm_checkout.checkout_manager');
            $sc = $checkoutManager->checkout($sc);
            $order = $checkoutManager->shoppingCartToOrder($sc);
            $user = $this->getCurrentUser();
            $order->setUser($user);
            $this->get('mqm_order.order_manager')->saveOrder($order, true);
            $this->get('mqm_cart.cart_manager')->removeAllItemsFromCart($sc);
            $this->postOrderProcess($order);

            return true;
            
        } catch (Exception $e) {
            return false;
        }
    }

    private function postOrderProcess(OrderInterface $order)
    {
        $this->get('mqm_statistic.logger.order')->logStatistic(array('order' => $order));
        $this->get('mqm_shop.order_notificator')->sendOrderPlacedNotification($order);
    }
    
    /**
     * Get the ShoppingCart from the logged user
     * If the user does NOT have a shoppingCart then one is created and attached to user but not persisted to database
     * 
     * @return ShoppingCart 
     */
    protected function getUserShoppingCart() {
        $user = $this->getCurrentUser();        
        $shoppingCart = null;
        if ($this->get('mqm_user.user_resolver')->isLoggedIn($user)) {
            $shoppingCart = $user->getShoppingCart();
            if ($shoppingCart == null) {
                $shoppingCart = $this->get('mqm_cart.cart_manager')->createCart();
                $user->setShoppingCart($shoppingCart);
            }
        } 
        
        return $shoppingCart;
    }
    
    public function getCurrentUser(){
        return $this->get('mqm_user.user_resolver')->getCurrentUser();
    }
    
    private function createSortManager()
    {
        $sortManager = $this->get('mqm_sort.sort_manager');
        $sortManager->addSort('pedido', 'publicId', 'pedido')
                    ->addSort('fecha', 'createdAt', 'Fecha', 'DESC', array('default' => true))
                    ->addSort('cantidad', 'quantity', 'Cantidad')
                    ->addSort('importe', 'totalPrice', 'Importe')
                    ->addSort('estado', 'status', 'Estado')
                    ->init();
        
        return $sortManager;
    }
}
