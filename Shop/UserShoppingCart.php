<?php

namespace MQM\ShopBundle\Shop;

use MQM\ShoppingCartBundle\Model\ShoppingCartManagerInterface;
use MQM\UserBundle\Helper\UserAuthenticatedResolverInterface;
use MQM\ProductBundle\Model\ProductInterface;

class UserShoppingCart
{    
    private $userResolver;
    private $shoppingCartManager;    
    
    public function __construct(ShoppingCartManagerInterface $shoppingCart, UserAuthenticatedResolverInterface $userResolver)
    {
        $this->userResolver = $userResolver;
        $this->shoppingCartManager = $shoppingCart;
    }
    
    public function addProduct(ProductInterface $product)
    {
        $shoppingCart = $this->getUserShoppingCart();
        $this->shoppingCartManager->addProductToCart($shoppingCart, $product);
        $this->shoppingCartManager->saveCart($shoppingCart);
        
        return $this;
    }
    
    public function getUserShoppingCart() {
        $user = $this->getCurrentUser();        
        $shoppingCart = null;
        if ($this->userResolver->isLoggedIn($user)) {
            $shoppingCart = $user->getShoppingCart();
            if ($shoppingCart == null) {
                $shoppingCart = $this->shoppingCartManager->createCart();
                $user->setShoppingCart($shoppingCart);
            }
        } 
        
        return $shoppingCart;
    }    
    
    private function getCurrentUser()
    {
        return $this->userResolver->getCurrentUser();
    }
}