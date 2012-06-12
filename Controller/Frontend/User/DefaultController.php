<?php

namespace MQM\ShopBundle\Controller\Frontend\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MQM\UserBundle\Model\UserInterface;

/**
 * @Route("/tienda/usuario")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/ver.{_format}", defaults={"_format"="html"}, name="TKShopFrontendUserShow")
     * @Template()
     */
    public function showAction($_format)
    {        
        $userResolver = $this->get('mqm_user.user_resolver');
        $user = $userResolver->getCurrentUser();
        $discountRule = $this->getDiscountRuleByUser($user);
        $user->setDiscountRule($discountRule);
        return $this->render("MQMShopBundle:Frontend\User\Default:show.".$_format.".twig", array(
            'user' => $user,
            ));
    } 
    
    private function getDiscountRuleByUser(UserInterface $user)
    {
        $email = $user->getEmail();
        
        return $this->getDiscountRuleByEmail($email);
    }
    
    private function getDiscountRuleByEmail($email)
    {
       $discountRule = $this->getDiscountRuleBy(array(
           'email' => $email,
       ));
       if ($discountRule->getEmail() == null) {
           $discountRule->setEmail($email);
       }

       return $discountRule;
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
    
    /**
     * @return \MQM\PricingBundle\Model\DiscountRule\DiscountRuleManagerInterface
     */
    private function getDiscountManager()
    {
        $discountRegistry = $this->get('mqm_pricing.type_manager_registry');
        $discountManager = $discountRegistry->getDiscountRuleManager('MQM\PricingBundle\Entity\DiscountRule\DiscountByUserRule');

        return $discountManager;
    }
}
