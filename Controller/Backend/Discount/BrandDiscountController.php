<?php

namespace MQM\ShopBundle\Controller\Backend\Discount;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MQM\ShopBundle\Form\Type\DiscountByBrandRuleType;

/**
 * @Route("/admin/descuentos/marca")
 */
class BrandDiscountController extends Controller
{
    /**
     * @Route("/editar_todos", name="TKShopBackendBrandDiscountEditAll")
     * @Template()
     */
    public function editAllAction()
    {
        $brands = $this->getBrands();
        $forms = $this->createDiscountFormsByBrands($brands);
        
        return array(
            'discountForms' => $forms,
            'brands' => $brands,
        );
    }

    /**
     * @Route("/{id}/actualizar_descuento", name="TKShopBackendBrandDiscountUpdate")
     * @Method("post")
     * @Template("MQMShopBundle:Backend\Discount\PortalDiscountAndIva:editAll.html.twig")
     */
    public function updateDiscountAction($id)
    {
        $discountRule = $this->getDiscountRuleByBrandId($id);
        $editForm   = $this->createForm(new DiscountByBrandRuleType(), $discountRule);
        $request = $this->getRequest();
        $editForm->bindRequest($request);
        if ($editForm->isValid()) {
            $this->getDiscountManager()->saveDiscountRule($discountRule);

            return $this->redirect($this->generateUrl('TKShopBackendBrandDiscountEdit'));
        }
        
        throw new \Exception('Invalid Brand DiscountRule');
    }

    private function getDiscountRuleBy(array $criteria)
    {
        $discountManager = $this->getDiscountManager();
        $discountRule = $discountManager->findDiscountRuleBy($criteria);
        if ($discountRule == null) {
            $discountRule = $discountManager->createDiscountRule();
        }

        return $discountRule;
    }

    private function createDiscountFormsByBrands(array $brands)
    {
        $forms = array();
        if ($brands != null) {
            foreach ($brands as $brand) {
                $discountRule = $this->getDiscountRuleByBrandId($brand->getId());
                $form = $this->createForm(new DiscountByBrandRuleType(), $discountRule);
                $forms[] = $form->createView();
            }
        }

        return $forms;
    }

   private function getDiscountRuleByBrandId($brandId)
   {
       $discountRule = $this->getDiscountRuleBy(array(
           'brandId' => $brandId,
       ));
       if ($discountRule->getBrandId() == null) {
           $discountRule->setBrandId($brandId);
       }

       return $discountRule;
   }

    private function getBrands()
    {
        $brandManager = $this->getBrandManager();
        $paginationManager = $this->get('mqm_pagination.pagination_manager');
        $brands = $brandManager->findBrands($paginationManager);

        if ($brands == null) {
            $brands = array();
        }

        return $brands;
    }

    /**
     * @return \MQM\PricingBundle\Model\DiscountRule\DiscountRuleManagerInterface
     */
    private function getDiscountManager()
    {
        $discountRegistry = $this->get('mqm_pricing.type_manager_registry');
        $discountManager = $discountRegistry->getDiscountRuleManager('MQM\PricingBundle\Entity\DiscountRule\DiscountByBrandRule');

        return $discountManager;
    }

    /**
     * @return \MQM\BrandBundle\Model\BrandManagerInterface
     */
    private function getBrandManager()
    {
        return $this->get('mqm_brand.brand_manager');
    }
}
