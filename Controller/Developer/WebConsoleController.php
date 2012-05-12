<?php

namespace MQM\ShopBundle\Controller\Developer;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/developer")
 */
class WebConsoleController extends Controller
{
    /**
     * @Route("/", name="TKShopDeveloperIndex")
     * @Template()
     */
    public function indexAction()
    {
        $form = $this->createExeForm();
        return array('form' => $form->createView());
    }
    
    /**
     * @Route("/exec.{_format}", name="TKShopDeveloperExec", defaults={"_format" = "html"})
     */
    public function execAction($_format)
    {
        $form = $this->createExeForm();
        $request = $this->get('request');
        $input = null;
        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $input = $data['input'];
                
               $commands = $this->get('session')->get('console.commands');
                if($input == 'clear'){
                   if(is_array($commands)){
                        $commands = array();                        
                   }
                }
                
                $command = array(
                    'input' => $input,
                    'output' => array()
                );
                exec('cd ../; ' . $input, $command['output']);
                
                $commands[] = $command;
                $this->get('session')->set('console.commands', $commands);
                $this->get('session')->save();
            }
        }

        return $this->render("MQMShopBundle:Developer\WebConsole:index.".$_format.".twig", array(
                'form' => $form->createView()
                )
        );
    }

    /**
     * @Route("/upgrade_products.{_format}", name="TKShopDeveloperExec", defaults={"_format" = "html"})
     */
    public function upgradeProductsAction($_format)
    {
        $priceUpgrade = $this->get('mqm_upgrade.price_upgrade');
        $priceUpgrade->upgradeProductPricesInDB();

        return new \Symfony\Component\HttpFoundation\Response('Prices Upgraded');
    }
     
    public function createExeForm($defaultinput = "")
    {        
        $arr = array(
        'input' => $defaultinput);
        $form = $this->createFormBuilder($arr)
            ->add('input', 'text')
            ->getForm();
            
        return $form;
    }
}
