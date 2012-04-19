<?php

namespace MQM\ToolsBundle\Test;

use MQM\ProductBundle\Model\ProductManagerInterface;
use Symfony\Bundle\FrameworkBundle\Tests\Functional\AppKernel;

class ShopBasicTest extends \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
{   
    protected $_container;

    public function __construct()
    {
        parent::__construct();
        
        $client = static::createClient();
        $container = $client->getContainer();
        $this->_container = $container;  
    }
    
    protected function setUp()
    {
    }

    protected function tearDown()
    {
    }

    protected function get($service)
    {
        return $this->_container->get($service);
    }
    
    public function testDependencies()
    {        
        $manager = $this->pricingManager = $this->get('mqm_user.user_manager');
        $this->assertNotNull($manager);
        
        $manager = $this->productManager = $this->get('mqm_product.product_manager');
        $this->assertNotNull($manager);
        
        $manager = $this->pricingManager = $this->get('mqm_pricing.pricing_manager');
        $this->assertNotNull($manager);
        
        $manager = $this->productManager = $this->get('mqm_category.category_manager');
        $this->assertNotNull($manager);
        
        $manager = $this->pricingManager = $this->get('mqm_cart.cart_manager');
        $this->assertNotNull($manager);
        
        $manager = $this->pricingManager = $this->get('mqm_order.order_manager');
        $this->assertNotNull($manager);
        
        /*$manager = $this->pricingManager = $this->get('mqm_sort.sort_manager');
        $this->assertNotNull($manager);
        
        $manager = $this->pricingManager = $this->get('mqm_pagination.pagination_manager');
        $this->assertNotNull($manager);*/
    }
}
