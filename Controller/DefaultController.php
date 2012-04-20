<?php

namespace MQM\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function staticAction($page)
    {
        return $this->render("MQMShopBundle:Default:$page.html.twig");
    }
}
