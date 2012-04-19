<?php

namespace MQM\ShopBundle\Controller\Frontend\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MQM\ShopBundle\Form\Shop\OrderType;

/**
 * Shop\Order controller.
 *
 * @Route("/tienda/usuario/pedidos")
 */
class OrderController extends Controller
{
    /**
     * Lists all Shop\Order entities.
     *
     * @Route("/", name="TKShopFrontendOrderIndex")
     * @Template()
     */
    public function indexAction()
    {
        $sortManager = $this->createSortManager();     
        $entities = $this->get('mqm_order.order_manager')->findOrders();

        return array(
            'entities' => $entities,
            'sortManager' => $sortManager->switchMode(),
            );
    }
    
     /**
     * 
     *
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
     * 
     *
     * @Route("/en_proceso", name="TKShopFrontendOrdersShowInProcess")
     * @Template()
     */
    public function showInProcessAction()
    {
        $sortManager = $this->createSortManager();        
        $user = $this->get('mqm_user.user_manager')->getCurrentUser();
        $entities = $this->get('mqm_order.order_manager')->findInProcessOrdersByUserId($user->getId(), $sortManager);

        return array(
            'entities' => $entities,
            'sortManager' => $sortManager->switchMode(),
        );
    }

    /**
     * Finds and displays a Shop\Order entity.
     *
     * @Route("/{publicId}/show", name="TKShopFrontendOrderShow")
     * @Template()
     */
    public function showAction($publicId)
    {
        $entity = $this->get('mqm_order.order_manager')->findOrderByPublicId($publicId);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Shop\Order entity.');
        }

        return array(
            'order'      => $entity,
        );
    }
    
    /**
     * Finds and displays a Shop\Order entity.
     *
     * @Route("/confirmar", name="TKShopFrontendOrderCreateFromShoppingCart")
     * @Template()
     */
    public function createFromShoppingCartAction()
    {
        $confirmed = $this->confirmOrder();
        
        if ($confirmed == true) {
                return $this->redirect($this->generateUrl('TKShopFrontendOrdersShowInProcess'));
        }
        
        else{
            return $this->redirect($this->generateUrl('TKShopFrontendUserShoppingCartEdit'));
        }
    }
  
    //  HELPER FUNCTIONS  //
    
    /**
     * Redirects to home page if there is a problem with Oder
     *
     * @param type $msg
     * @param type $redirect
     * @return type 
     */
    protected function orderErrorHandler($msg = null, $redirect=null){
        //TODO: Redirect to index
        $this->get('session')->setFlash('order_error',"Atencion: El usuario no puede tener pedidos, cree un usuario de tipo cliente");
        return $this->redirect($this->generateUrl("TKShopFrontendIndex"));
    }
    
    /**
     * Get the Order from the logged user
     * 
     * @return Order 
     */
    protected function getOrderFromCurrentUser(){
        $user = $this->getCurrentUser();
        $shoppingCart = null;
        if($this->get('mqm_user.user_manager')->isDBUser($user)){
            return $user->getOrders();            
        }
        else{
            return null;
        }
    }
    
    
    /**
     *
     * @param ShoppingCart $shoppingCart
     * @return boolean 
     */
    public function confirmOrder() {
        
        $sc = $this->getUserShoppingCart();

        $items = $sc->getItems();
        if (count($items) < 1) {
            return false;
        }
        try {
            $checkoutManager = $this->get('mqm_checkout.checkout_manager');
            $sc = $checkoutManager->checkout($sc);
            // generate an order
            $order = $checkoutManager->shoppingCartToOrder($sc);

            $user = $this->getCurrentUser();

            $order->setUser($user);

            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($order);
            $em->flush();
            //End generating an order
            // remove all cart items
            $this->get('mqm_cart.cart_manager')->removeAllItemsFromCart($sc);
            $em->flush();
            //End removing all cart items
            return true;
            
        } catch (Exception $e) {
            return false;
        }
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
        if ($this->get('mqm_user.user_manager')->isDBUser($user)) {
            $shoppingCart = $user->getShoppingCart();
            if ($shoppingCart == null) {
                $shoppingCart = new ShoppingCart();
                $user->setShoppingCart($shoppingCart);
            }
        } 
        
        return $shoppingCart;
    }
    
    public function getCurrentUser(){
        return $this->get('mqm_user.user_manager')->getCurrentUser();
    }
    
    private function createSortManager()
    {
        $sortManager = $this->get('mqm_sort.sort_manager');
        $sortManager->addSort('pedido', 'publicId', 'pedido')
                    ->addSort('fecha', 'createdAt', 'Fecha')
                    ->addSort('cantidad', 'quantity', 'Cantidad')
                    ->addSort('importe', 'totalPrice', 'Importe')
                    ->addSort('estado', 'status', 'Estado')
                    ->init();
        
        return $sortManager;
    }
}
