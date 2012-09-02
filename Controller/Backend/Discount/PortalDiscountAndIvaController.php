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
     * @Route("/ver_todos", name="TKShopBackendPortalDiscountAndIvaEdit")
     * @Template()
     */
    public function editAllAction()
    {
        $discountRule = $this->getDefaultDiscountRule();
        $discountForm = $this->createForm(new DiscountByPortalRuleType(), $discountRule);
        $taxForm = $this->createTaxForm();
        
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
        $form   = $this->createForm(new DiscountByPortalRuleType(), $discountRule);
        $request = $this->getRequest();
        $form->bind($request);
        if ($form->isValid()) {
            $this->getDiscountManager()->saveDiscountRule($discountRule);

            return $this->redirect($this->generateUrl('TKShopBackendPortalDiscountAndIvaEdit'));
        }
        
        throw new \Exception('Invalid Portal DiscountRule');
    }
    
    /**
     * @Route("/actualizar_iva", name="TKShopBackendPortalTaxUpdate")
     * @Method("post")
     * @Template("MQMShopBundle:Backend\Discount\PortalDiscountAndIva:editAll.html.twig")
     */
    public function updateTaxAction()
    {
        $form = $this->createTaxForm();
        $request = $this->getRequest();
        $form->bind($request);
        if ($form->isValid()) {            
            $this->getTaxationManager()->saveTax($form->getData()->getTax());

            return $this->redirect($this->generateUrl('TKShopBackendPortalDiscountAndIvaEdit'));
        }

        throw new \Exception('Invalid Portal DiscountRule');
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

    private function createTaxForm()
    {
        $taxObject = new Tax($this->getTaxationManager()->getTax());
        $taxForm = $this->createFormBuilder($taxObject)
            ->add('tax')
            ->getForm();

        return $taxForm;
    }
}

class Tax
{
    var $tax = 0;

    function __construct($p_tax)
    {
        $this->tax = $p_tax;
    }

    public function setTax($tax)
    {
        $this->tax = $tax;
    }

    public function getTax()
    {
        return $this->tax;
    }
}
