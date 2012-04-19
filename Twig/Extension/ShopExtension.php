<?php

namespace MQM\ShopBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Router;

class ShopExtension extends \Twig_Extension
{
    private $container;
    private $router;

    public function __construct(ContainerInterface $container, Router $router)
    {
        $this->container = $container;
        $this->router = $router;
    }
    
    public function getName()
    {
        return 'mqm_shop.twig_extension';
    }

    public function getFunctions()
    {
        return array(
            'mqm_shop_app_path' => new \Twig_Function_Method($this, 'appPath'),
        );
    }
    
    public function getFilters()
    {
        return array(
            'mqm_shop_to_app_path' => new \Twig_Filter_Method($this, 'toAppPath'),
        );
    }
    
    public function appPath($controllerName, $parameters) 
    {
        $url = $this->router->generate($controllerName, $parameters);
        
        return $this->toAppPath($url);
    }
    
    public function toAppPath($path)
    {
        try {
            $baseUrl = $this->container->get('request')->getBaseUrl();
            $basePos = strpos($path, $baseUrl);
            if ($basePos === false) {
                return $path;
            }
            else {
                $lenBase = $basePos + strlen($baseUrl);
            }        
            $lenPath = strlen($path);
            $path = substr($path, $lenBase, $lenPath -1);
        }
       catch (\Exception $e){}
       
       return $path;
    }    
}