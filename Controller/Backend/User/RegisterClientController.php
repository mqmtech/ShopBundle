<?php

namespace MQM\ShopBundle\Controller\Backend\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MQM\ShopBundle\Form\Type\UserType;
use MQM\UserBundle\Entity\User;
use MQM\UserBundle\Model\UserInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use MQM\UserBundle\Form\Type\UserRegistrationType;

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
        $form = $this->createForm(new UserRegistrationType(), null, array('validation_groups' => 'Registration'));
        $isValid = $staffRegistrationFormHandler->process($form);
        if ($isValid) {
            $user = $form->getData();
            $user->setIsEnabled(true);
            $this->get('mqm_user.user_manager')->saveUser($user);

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
        $user = $this->get('mqm_user.user_manager')->findUserBy(array('id' => $id));
        if (!$user) {
            throw $this->createNotFoundException('Unable to find Shop\User entity.');
        }
        $form = $this->createForm(new UserRegistrationType(), $user, array('validation_groups' => 'Edition'));
        $staffRegistrationFormHandler = $this->get('mqm_user.form.handler.user_registration');
        $isValid = $staffRegistrationFormHandler->process($form);
        if ($isValid) {
            $this->get('mqm_user.user_manager')->saveUser($user);

            return $this->redirect($this->generateUrl('TKShopBackendUserClientShowAll'));
        }

        return array(
            'entity' => $user,
            'form' => $form->createView(),
        );
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

}
