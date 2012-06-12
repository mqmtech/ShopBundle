<?php

namespace MQM\ShopBundle\Controller\Frontend\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use MQM\ShopBundle\Form\Type\ShoppingCartType;
use MQM\ShoppingCartBundle\Model\ShoppingCartInterface;

/**
 * @Route("/tienda/usuario/carrito")
 */
class ShoppingCartController extends Controller 
{
    /**
     * @Route("/ver.{_format}", defaults={"_format"="html"}, name="TKShopFrontendUserShoppingCartShow")
     * @Template()
     */
    public function showAction()
    {
        $cart = $this->getUserShoppingCart();
        $checkoutManager = $this->get('mqm_checkout.checkout_manager');
        $cart = $checkoutManager->checkout($cart);
        
        return array(
            'entity' => $cart,
        );
    }

    /**
     * @Route("/editar", name="TKShopFrontendUserShoppingCartEdit")
     */
    public function editAction()
    {
        $cart = $this->getUserShoppingCart();
        $checkoutManager = $this->get('mqm_checkout.checkout_manager');
        $cart = $checkoutManager->checkout($cart);
        $form = $this->createForm(new ShoppingCartType(), $cart);
        
        return $this->render("MQMShopBundle:Frontend\User\ShoppingCart:edit.html.twig", array(
                    'entity' => $cart,
                    'form' => $form->createView(),
                ));
    }    
    
    /**
     * @Route("/update", name="TKShopFrontendUserShoppingCartUpdate")
     * @Method("post")
     * @Template("MQMShopBundle:Frontend\User\ShoppingCart:edit.html.twig")
     */
    public function updateAction()
    {
        $cart = $this->getUserShoppingCart();
        $form = $this->createForm(new ShoppingCartType(), $cart);
        $request = $this->getRequest();
        $form->bindRequest($request);
        if ($form->isValid()) {
            $cart = $this->get('mqm_cart.cart_manager')->removeItemsWithoutProductsFromCart($cart);
            $this->get('mqm_cart.cart_manager')->saveCart($cart);
        }
        
        return $this->redirect($this->generateUrl('TKShopFrontendUserShoppingCartEdit'));
    }
    
    /**
     * @Route("/previsualizar_orden", name="TKShopFrontendUserShoppingCartPreview")
     * @Method("post")
     * @Template("MQMShopBundle:Frontend\User\ShoppingCart:edit.html.twig")
     */
    public function previewAction()
    {
        $cart = $this->getUserShoppingCart();
        $form = $this->createForm(new ShoppingCartType(), $cart);
        $request = $this->getRequest();
        $form->bindRequest($request);
        if ($form->isValid()) {
            $cart = $this->get('mqm_cart.cart_manager')->removeItemsWithoutProductsFromCart($cart);
            $this->get('mqm_cart.cart_manager')->saveCart($cart);
            if (!$this->get('mqm_cart.cart_manager')->isEmpty($cart)) {
                $order = $this->generateOrderFromCart($cart);
                
                return $this->render("MQMShopBundle:Frontend\User\ShoppingCart:preview.html.twig", 
                    array(
                        'order' => $order
                    )
                );
            }
        }
        
        return $this->redirect($this->generateUrl('TKShopFrontendUserShoppingCartEdit'));
    }
    
    private function generateOrderFromCart(ShoppingCartInterface $cart)
    {
        $checkoutManager = $this->get('mqm_checkout.checkout_manager');
        $cart = $checkoutManager->checkout($cart);
        $order = $checkoutManager->shoppingCartToOrder($cart);
        
        return $order;
    }

    /**
     * @Route("/item/{id}/eliminar", name="TKShopFrontendUserShoppingCartDeleteItem")
     */
    public function deleteItemAction($id)
    {
        $shoppingCart = $this->getUserShoppingCart();
        $cartManager = $this->get('mqm_cart.cart_manager');
        $cartManager->removeItemFromCart($shoppingCart, $id);
        $cartManager->saveCart($shoppingCart);

        return $this->redirect($this->generateUrl('TKShopFrontendUserShoppingCartEdit'));
    }

    /**
     * @Route("/agregar_producto/{id}/", name="TKShopFrontendUserShoppingAddProduct")
     */
    public function addProductAction($id)
    {
        $product = $this->get('mqm_product.product_manager')->findProductBy(array('id' => $id));
        if ($product == null) {
            throw new Exception("Custom Exception: Product with id $id does NOT exist in database");
        }
        $this->get('mqm_shop.user_cart')->addProduct($product);
        
        $session = $this->get('session');
        $last_route = $session->get('last_route', array('name' => 'TKShopFrontendIndex'));
        return ($this->redirect($this->generateUrl($last_route['name'], $last_route['params'])));    
    }    

    private function getUserShoppingCart()
    {
        return $this->get('mqm_shop.user_cart')->getUserShoppingCart();
    }
    
    private function getCurrentUser()
    {        
        return $this->get('mqm_user.user_resolver')->getCurrentUser();
    }
}
