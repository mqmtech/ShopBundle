<?php

namespace MQM\ShopBundle\Controller\Backend\Discount;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MQM\ShopBundle\Form\Type\DiscountByPortalRuleType;

/**
 * @Route("/admin/descuentos/portal_iva")
 */
class PortalDiscountAndIvaController extends Controller
{
    /**
     * @Route("/ver_todos", name="TKShopBackendPortalDiscountAndIvaShow")
     * @Template()
     */
    public function showAllAction()
    {
        $discountRule = $this->getDefaultDiscountRule();
        $discountForm = $this->createForm(new DiscountByPortalRuleType(), $discountRule);
        
        $taxValue = $this->getTaxationManager()->getTax();
        $taxObject = array('tax' => $taxValue);
        $taxForm = $this->getTaxForm($taxObject);
        
        return array(
            'discountForm' => $discountForm->createView(),
            'taxForm' => $taxForm->createView(),
        );      
    }

    /**
     * @Route("/{id}/actualizar_descuento", name="TKShopBackendPortalDiscountUpdate")
     * @Method("post")
     * @Template("MQMShopBundle:Backend\Discount\PortalDiscountAndIva:showAll.html.twig")
     */
    public function updateDiscountAction($id)
    {
        $discountRule = $this->getDefaultDiscountRule();
        $editForm   = $this->createForm(new DiscountByPortalRuleType(), $discountRule);
        $request = $this->getRequest();
        $editForm->bindRequest($request);
        if ($editForm->isValid()) {
            $this->getDiscountManager()->saveDiscountRule($discountRule);

            return $this->redirect($this->generateUrl('TKShopBackendPortalDiscountAndIvaShow'));
        }
        
        throw new \Exception('Invalid Portal DiscountRule');
    }
    
    /**
     * @Route("/actualizar_iva", name="TKShopBackendPortalTaxUpdate")
     * @Method("post")
     * @Template("MQMShopBundle:Backend\Discount\PortalDiscountAndIva:showAll.html.twig")
     */
    public function updateTaxAction()
    {
        $taxValue = $this->getTaxationManager()->getTax();
        $taxObject = array('tax' => $taxValue);
        $editForm = $this->getTaxForm($taxObject);
        $request = $this->getRequest();
        $editForm->bindRequest($request);
        if ($editForm->isValid()) {
            throw new \Exception('tax: ' . $request->request->get('form[tax]'));
            throw new \Exception('tax: ' . $taxObject['tax']);
            $this->getTaxationManager()->saveTax($taxObject['tax']);

            return $this->redirect($this->generateUrl('TKShopBackendPortalDiscountAndIvaShow'));
        }
        
        throw new \Exception('Invalid Portal DiscountRule');
    }
    
    private function getTaxForm($tax)
    {
        $taxForm = $this->createFormBuilder($tax)
                ->add('tax')
                ->getForm();
        
        return $taxForm;
    }
    
    /**
     * @return \MQM\PricingBundle\Model\DiscountRule\DiscountRuleInterface 
     */
    private function getDefaultDiscountRule()
    {
        $discountManager =  $this->getDiscountManager();
        $discountRule = $discountManager->findDiscountRuleBy(array());
        if (!$discountRule) {
            $discountRule = $discountManager->createDiscountRule();
        }
        
        return $discountRule;
    }
    
    /**
     * @return \MQM\PricingBundle\Model\DiscountRule\DiscountRuleManagerInterface
     */
    private function getDiscountManager()
    {
        return $this->get('mqm_pricing.discount_by_portal_manager');
    }
    
    /**
     * @return \MQM\TaxationBundle\Taxation\TaxationManagerInterface
     */
    private function getTaxationManager()
    {
        return $this->get('mqm_taxation.taxation_manager');
    }
}
