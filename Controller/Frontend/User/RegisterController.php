<?php

namespace MQM\ShopBundle\Controller\Frontend\User;



use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MQM\UserBundle\Entity\User;
use MQM\UserBundle\Model\UserInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use MQM\UserBundle\Form\Type\UserRegistrationType;

/**
 * @Route("/tienda/registro")
 */
class RegisterController extends Controller {
    /**
     * @Route("/new.{_format}", defaults={"_format"="html"}, name="TKShopFrontendUserNew")
     * @Template("MQMShopBundle:Frontend\User\Register:new.html.twig")
     * @Method({"post", "get"})
     */
    public function newAction($_format)
    {
        $staffRegistrationFormHandler = $this->get('mqm_user.form.handler.user_registration');
        $form = $this->createForm(new UserRegistrationType(), null, array('validation_groups' => 'Registration'));
        $isValid = $staffRegistrationFormHandler->process($form);
        $user = $form->getData();
        if ($isValid) {
            $this->get('mqm_user.user_manager')->saveUser($user);
            $this->get('mqm_shop.user_notificator')->sendUserRegistrationMessage($user);

            return $this->render("MQMShopBundle:Frontend\User\Register:successMessageOnCreate." . "html" . ".twig", array('user' => $user));
        }

        return array(
            'form' => $form->createView(),
            'entity' => $user,
        );
    }
}
