<?php

namespace MQM\ShopBundle\Controller\Frontend\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use MQM\ShopBundle\Form\Type\ShoppingCartType;
use MQM\ShoppingCartBundle\Model\ShoppingCartInterface;

/**
 * Frontend\Default controller.
 *
 * @Route("/tienda/usuario/carrito")
 */
class ShoppingCartController extends Controller 
{
    const FORM_ORDER_FIELD = "cart_order";
    const FORM_ORDER_UPDATE_VALUE= "cart_update";
    const FORM_ORDER_COMFIRM_VALUE = "cart_comfirm";

    /**
     * Frontend demo
     *
     * @Route("/", name="TKShopFrontendUserShoppingCartIndex")
     * @Template()
     */
    public function indexAction()
    {
        return $this->redirect($this->generateUrl('TKShopFrontendUserShoppingCartEdit'));
    }

    /**
     * Finds and displays a Shop\ShoppingCart entity.
     *
     * @Route("/ver.{_format}", defaults={"_format"="html"}, name="TKShopFrontendUserShoppingCartShow")
     * @Template()
     */
    public function showAction()
    {
        $entity = $this->getUserShoppingCart();

        if (!$entity) {
            //throw $this->createNotFoundException('Custom Error: Unable to find Shop\ShoppingCart entity.');
        }
        else{
            $checkoutManager = $this->get('mqm_checkout.checkout_manager');
            $entity = $checkoutManager->checkout($entity);
        }
        return array(
            'entity' => $entity,
        );
    }

    /**
     * Displays a form to create a new Shop\ShoppingCart entity.
     *
     * @Route("/new", name="TKShopFrontendUserShoppingCartNew")
     * @Template()
     */
    public function newAction()
    {
        $entity = $this->get('mqm_cart.cart_manager')->createCart();
        $form = $this->createForm(new ShoppingCartType(), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView()
        );
    }

    /**
     * Creates a new Shop\ShoppingCart entity.
     *
     * @Route("/create", name="TKShopFrontendUserShoppingCartCreate")
     * @Method("post")
     * @Template("MQMShopBundle:Frontend\ShoppingCart:new.html.twig")
     */
    public function createAction()
    {
        $entity = $this->get('mqm_cart.cart_manager')->createCart();
        $request = $this->getRequest();
        $form = $this->createForm(new ShoppingCartType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            /* $em->persist($entity);
              $em->flush(); */
            $this->persistAndFlushShoppingCart($entity);

            return $this->redirect($this->generateUrl('TKShopFrontendShoppingCartShow', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing Shop\ShoppingCart entity.
     *
     * @Route("/editar", name="TKShopFrontendUserShoppingCartEdit")
     */
    public function editAction()
    {
        $entity = $this->getUserShoppingCart();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Shop\ShoppingCart entity.');
        }

        // Fill Shopping Cart with the current last Market/DiscountRule Info
        $checkoutManager = $this->get('mqm_checkout.checkout_manager');
        $entity = $checkoutManager->checkout($entity);
        // End filling Shopping Cart

        $editForm = $this->createForm(new ShoppingCartType(), $entity);
        $deleteForm = $this->createDeleteForm($entity->getId());
        
        return $this->render("MQMShopBundle:Frontend\User\ShoppingCart:edit.html.twig", array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'order_field' => self::FORM_ORDER_FIELD,
                    'update_value' => self::FORM_ORDER_UPDATE_VALUE,
                    'comfirm_value' => self::FORM_ORDER_COMFIRM_VALUE,
                ));
    }

    /**
     * Displays a form to edit an existing Shop\ShoppingCart entity.
     *
     * @Route("/item/{id}/eliminar", name="TKShopFrontendUserShoppingCartDeleteItem")
     */
    public function deleteItemAction($id)
    {
        $shoppingCart = $this->getUserShoppingCart();
        if ($shoppingCart != null) {

            $this->get('mqm_cart.cart_manager')->removeItemFromCart($shoppingCart, $id);
            $em = $this->getDoctrine()->getEntityManager();
            /* $em->persist($shoppingCart);
              $em->flush(); */
            $this->persistAndFlushShoppingCart($shoppingCart);

            return $this->redirect($this->generateUrl('TKShopFrontendUserShoppingCartEdit'));
            /* return $this->forward("MQMShopBundle:Frontend\User\ShoppingCart:edit", array(
              'id' => $shoppingCart->getId()
              )); */
        } else {
            $this->get('session')->setFlash('shoppingCart_error', "Atencion: El usuario no dispone de carrito de la compra");
            return $this->redirect($this->generateUrl("TKShopFrontendIndex"));
        }
    }

    /**
     * Deletes a Shop\ShoppingCart entity.
     *
     * @Route("/delete", name="TKShopFrontendUserShoppingCartDelete")
     * @Method("post")
     */
    public function deleteAction()
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $entity = $this->getUserShoppingCart();

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Shop\ShoppingCart entity.');
            }

            $this->removeShoppingCart($entity);
        }

        return $this->redirect($this->generateUrl('TKShopFrontendUserShoppingCartIndex'));
    }

    /**
     * Edits an existing Shop\ShoppingCart entity.
     *
     * @Route("/update", name="TKShopFrontendUserShoppingCartUpdate")
     * @Method("post")
     * @Template("MQMShopBundle:Frontend\User\ShoppingCart:edit.html.twig")
     */
    public function updateAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $this->getUserShoppingCart();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Shop\ShoppingCart entity.');
        }

        $editForm = $this->createForm(new ShoppingCartType(), $entity);
        $request = $this->getRequest();
        $editForm->bindRequest($request);
        if ($editForm->isValid()) {
            // Check if the quantity of an Entity is zero, then remove the entity
            $entity = $this->get('mqm_cart.cart_manager')->removeItemsWithoutProductsFromCart($entity);
            // End Check if the quantity of an Entity is zero
            $em->persist($entity);
            $em->flush();

            $request = Request::createFromGlobals();
            $orderField = $request->request->get(self::FORM_ORDER_FIELD);
            if ($orderField == self::FORM_ORDER_COMFIRM_VALUE) {
                if($this->isCartValid($entity)){
                    return $this->render("MQMShopBundle:Frontend\User\ShoppingCart:preview.html.twig", array(
                        'order' => $this->generateOrder($entity),
                        )
                    );
                }
               else{
                   
               }
            }
        }
        return $this->redirect($this->generateUrl('TKShopFrontendUserShoppingCartEdit'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm()
        ;
    }

    /**
     * @Route("/agregar_producto/{id}/", name="TKShopFrontendUserShoppingAddProduct")
     */
    public function addProductAction($id)
    {
        $shoppingCart = $this->getUserShoppingCart();
        if ($shoppingCart == null) {
            return $this->shoppingCartErrorHandler();
        }

        //Get Product from DB
        $em = $this->getDoctrine()->getEntityManager();
        $product = $this->get('mqm_product.product_manager')->findProductBy(array('id' => $id));
        if ($product == null) {
            throw new Exception("Custom Exception: Product with id $id does NOT exist in database");
        }
        $this->get('mqm_cart.cart_manager')->addProductToCart($shoppingCart, $product);
        $em->persist($shoppingCart);
        $em->flush();

        //return $this->redirect($this->generateUrl("TKShopFrontendUserShoppingCartIndex"));
        $session = $this->get('session');
        $last_route = $session->get('last_route', array('name' => 'TKShopFrontendIndex'));
        return ($this->redirect($this->generateUrl($last_route['name'], $last_route['params'])));    
    
    }

    /**
     * @Route("/limpiar_carrito/", name="TKShopFrontendUserShoppingCartRemoveAllItems")
     */
    public function removeAllProductsAction()
    {
        $shoppingCart = $this->getUserShoppingCart();

        if ($shoppingCart == null) {
            return $this->shoppingCartErrorHandler();
        }

        $this->get('mqm_cart.cart_manager')->removerAllItemsFromCart($shoppingCart);

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($shoppingCart);
        $em->flush();

        return $this->redirect($this->generateUrl("TKShopFrontendUserShoppingCartIndex"));
    }

    //  HELPER FUNCTIONS  //

    /**
     * Redirects to home page if there is a problem with ShoppingCart
     *
     * @param type $msg
     * @param type $redirect
     * @return type 
     */
    protected function shoppingCartErrorHandler($msg = null, $redirect=null)
    {
        $this->get('session')->setFlash('shoppingCart_error', "Atencion: El usuario no dispone de carrito, cree un usuario de tipo cliente");
        return $this->redirect($this->generateUrl("TKShopFrontendIndex"));
    }

    /**
     * Get the ShoppingCart from the logged user
     * If the user does NOT have a shoppingCart then one is created and attached to user but not persisted to database
     * 
     * @return ShoppingCart 
     */
    protected function getUserShoppingCart()
    {
        $user = $this->getCurrentUser();
        
        $shoppingCart = null;
        if ($this->get('mqm_user.user_manager')->isDBUser($user)) {
            $shoppingCart = $user->getShoppingCart();
            $shoppingCart = $this->get('mqm_cart.cart_manager')->refreshCart($shoppingCart);
            if ($shoppingCart == null) {
                $shoppingCart = $this->get('mqm_cart.cart_manager')->createCart();
                $user->setShoppingCart($shoppingCart);
            }
        } 
        
        return $shoppingCart;
    }
    
    public function isCartValid($sc)
    {
        $items = $sc->getItems();
        if (count($items) < 1) {
            return false;
        }
        return true;
    }
    
    public function generateOrder(ShoppingCartInterface $entity)
    {
        $checkoutManager = $this->get('mqm_checkout.checkout_manager');
            $entity = $checkoutManager->checkout($entity);
            // generate an order
            $order = $checkoutManager->shoppingCartToOrder($entity);
            return $order;
    }

    /**
     *
     * @param ShoppingCartInterface $shoppingCart 
     */
    protected function persistAndFlushShoppingCart(ShoppingCartInterface $shoppingCart) {
        $user = $this->getCurrentUser();
        if ($this->get('mqm_user.user_manager')->isDBUser($user)) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($shoppingCart);
            $em->flush();
        } else {
            $session = $this->get('session');
        }
    }

    /**
     *
     * @param ShoppingCartInterface $shoppingCart 
     */
    protected function removeShoppingCart(ShoppingCartInterface $shoppingCart) {
        if ($this->get('mqm_user.user_manager')->isDBUser($user)) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->remove($shoppingCart);
            $em->flush();
        } else {
            $session = $this->get('session');
            $session->set('shoppingCart', null);
            $session->save();
        }
    }
    
    public function getCurrentUser(){
        return $this->get('mqm_user.user_manager')->getCurrentUser();
    }

}
