<?php

namespace MQM\ShopBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\RouterInterface;

class SavePreviousPage
{
        private $router;
        private $ignoredRoutes;
 
       public function __construct(RouterInterface $router, array $ignoredRoutes)
        {
            $this->router = $router;
            $this->ignoredRoutes = $ignoredRoutes;
        }
 
        public function onKernelRequest(GetResponseEvent $event)
        {            
            if ($event->getRequestType() !== \Symfony\Component\HttpKernel\HttpKernel::MASTER_REQUEST) {
                return;
            }
   
            /** @var \Symfony\Component\HttpFoundation\Request $request  */
            $request = $event->getRequest();
            /** @var \Symfony\Component\HttpFoundation\Session $session  */
            $session = $request->getSession();
 
            $routeParams = $this->router->match($request->getPathInfo());
            $routeName = $routeParams['_route'];
            
            if ($routeName[0] == '_' || $this->inIgnoredRouters($routeName)) {
                return;
            }
            unset($routeParams['_route']);
            $routeData = array('name' => $routeName, 'params' => $routeParams);
 
            //Skipping duplicates
            $thisRoute = $session->get('this_route', array());
            if ($thisRoute == $routeData) {
                return;
            }
            $session->set('last_route', $thisRoute);
            $session->set('this_route', $routeData);
            $session->save();
        }
        
        public function inIgnoredRouters($routeName){
            for ($index = 0; $index < count($this->ignoredRoutes); $index++) {
                $ignoredRoute = $this->ignoredRoutes[$index];
                if($routeName == $ignoredRoute){
                    return true;
                }
            }
            return false;
        }
}