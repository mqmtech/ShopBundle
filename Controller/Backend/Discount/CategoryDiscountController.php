<?php

namespace MQM\ShopBundle\Controller\Backend\Discount;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MQM\ShopBundle\Form\Type\DiscountByCategoryRuleType;

/**
 * @Route("/admin/descuentos/categoria")
 */
class CategoryDiscountController extends Controller
{
    /**
     * @Route("/editar_todos", name="TKShopBackendCategoryDiscountEditAll")
     * @Template()
     */
    public function editAllAction()
    {
        $categories = $this->getAllFamilies();
        $forms = $this->createDiscountFormsByCategories($categories);
        
        return array(
            'discountForms' => $forms,
            'categories' => $categories,
        );
    }

    /**
     * @Route("/{id}/editar_subcategoria", name="TKShopBackendCategoryDiscountEditAllSubcategories")
     * @Template()
     */
    public function editAllSubCategoriesAction($id)
    {
        $categories = $this->getSubcategories($id);
        $forms = $this->createDiscountFormsByCategories($categories);
        $parentCategory = $this->getCategoryManager()->findCategoryBy(array(
            'id' => $id,
        ));

        return $this->render('MQMShopBundle:Backend\Discount\CategoryDiscount:editAll.html.twig',
            array(
                'discountForms' => $forms,
                'categories' => $categories,
                'parentCategory' => $parentCategory
            ));
    }

    /**
     * @Route("/{id}/actualizar_descuento", name="TKShopBackendCategoryDiscountUpdate")
     * @Method("post")
     * @Template("MQMShopBundle:Backend\Discount\PortalDiscountAndIva:editAll.html.twig")
     */
    public function updateDiscountAction($id)
    {
        $discountRule = $this->getDiscountRuleByCategoryId($id);
        $editForm   = $this->createForm(new DiscountByCategoryRuleType(), $discountRule);
        $request = $this->getRequest();
        $editForm->bindRequest($request);
        if ($editForm->isValid()) {
            $this->getDiscountManager()->saveDiscountRule($discountRule);

            return $this->redirect($this->generateUrl('TKShopBackendCategoryDiscountEdit'));
        }
        
        throw new \Exception('Invalid Category DiscountRule');
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

    private function createDiscountFormsByCategories($categories)
    {
        $forms = array();
        if ($categories != null) {
            foreach ($categories as $category) {
                $discountRule = $this->getDiscountRuleByCategoryId($category->getId());
                $form = $this->createForm(new DiscountByCategoryRuleType(), $discountRule);
                $forms[] = $form->createView();
            }
        }

        return $forms;
    }

   private function getDiscountRuleByCategoryId($categoryId)
   {
       $discountRule = $this->getDiscountRuleBy(array(
           'categoryId' => $categoryId,
       ));
       if ($discountRule->getCategoryId() == null) {
           $discountRule->setCategoryId($categoryId);
       }

       return $discountRule;
   }

    private function getAllFamilies()
    {
        $paginationManager = $this->get('mqm_pagination.pagination_manager');
        $categories = $this->getCategoryManager()->findAllFamilies($paginationManager);

        return $categories;
    }

    private function getSubcategories($parentCategoryId)
    {
        $category = $this->getCategoryManager()->findCategoryBy(array('id' => $parentCategoryId));
        if ($category != null) {
            $categories = $category->getCategories();
            if($categories != null){
                $totalItems = count($categories);
                $paginationManager = $this->get('mqm_pagination.pagination_manager');
                $paginationManager->init($totalItems);
                $categories = $paginationManager->paginateArray($categories);
            }
        }

        return $categories;
    }

    /**
     * @return \MQM\PricingBundle\Model\DiscountRule\DiscountRuleManagerInterface
     */
    private function getDiscountManager()
    {
        $discountRegistry = $this->get('mqm_pricing.type_manager_registry');
        $discountManager = $discountRegistry->getDiscountRuleManager('MQM\PricingBundle\Entity\DiscountRule\DiscountByCategoryRule');

        return $discountManager;
    }

    /**
     * @return \MQM\CategoryBundle\Model\CategoryManagerInterface
     */
    private function getCategoryManager()
    {
        return $this->get('mqm_category.category_manager');
    }
}
