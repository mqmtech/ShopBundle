<?php

namespace MQM\ShopBundle\Controller\Backend\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Exception;
use MQM\UserBundle\Entity\User;
use MQM\UserBundle\Model\UserInterface;
use MQM\PricingBundle\Model\DiscountRule\DiscountRuleManagerInterface;
use MQM\ShopBundle\Form\Type\UserRegistrationBackendType;

/**
 * @Route("/admin/registro_clientes")
 */
class RegisterClientController extends Controller
{
    /**
     * @Route("/new.{_format}", defaults={"_format"="html"}, name="TKShopBackendUserNew")
     * @Template("MQMShopBundle:Backend\User\RegisterClient:new.html.twig")
     * @Method({"post", "get"})
     */
    public function newAction($_format)
    {
        $staffRegistrationFormHandler = $this->get('mqm_user.form.handler.user_registration');
        $form = $this->createForm(new UserRegistrationBackendType(), null, array('validation_groups' => 'Registration'));
        $isValid = $staffRegistrationFormHandler->process($form);
        if ($isValid) {
            $user = $form->getData();
            $user->setIsEnabled(true);
            $this->saveUserRegistration($user);

            return $this->redirect($this->generateUrl('TKShopBackendUserClientShowAll'));
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/{id}/edit", name="TKShopBackendUserEdit")
     * @Template("MQMShopBundle:Backend\User\RegisterClient:edit.html.twig")
     * @Method({"post", "get"})
     */
    public function editAction($id)
    {
        $user = $this->getUserRegistrationByUserId($id);
        $form = $this->createForm(new UserRegistrationBackendType(), $user, array('validation_groups' => 'Edition'));
        $staffRegistrationFormHandler = $this->get('mqm_user.form.handler.user_registration');
        $isValid = $staffRegistrationFormHandler->process($form);
        if ($isValid) {
            $this->saveUserRegistration($user);

            return $this->redirect($this->generateUrl('TKShopBackendUserClientShowAll'));
        }

        return array(
            'entity' => $user,
            'form' => $form->createView(),
        );
    }
    
    private function saveUserRegistration(UserInterface $user)
    {
        $this->get('mqm_user.user_manager')->saveUser($user);
        
        $userDiscountRule = $user->getDiscountRule();
        $this->getDiscountManager()->saveDiscountRule($userDiscountRule);
    }
    
    private function getUserRegistrationByUserId($id)
    {
        $user = $this->get('mqm_user.user_manager')->findUserBy(array('id' => $id));
        if (!$user) {
            throw $this->createNotFoundException('Unable to find Shop\User entity.');
        }
        
        $email = $user->getEmail();
        $userDiscount = $this->getDiscountRuleByEmail($email);
        $user->setDiscountRule($userDiscount);
        
        return $user;
    }

    /**
     * @Route("/{id}/validar.{_format}", defaults={"_format"="html"}, name="TKShopBackendUserValidate")
     * @Template()
     */
    public function validateAction($id, $_format)
    {
        $userManager = $this->get('mqm_user.user_manager');
        $entity = $userManager->findUserBy(array('id' => $id));
        $entity->setIsEnabled(true);
        $userManager->saveUser($entity);

        return $this->redirect($this->generateUrl('TKShopBackendUserClientIndex'));
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
