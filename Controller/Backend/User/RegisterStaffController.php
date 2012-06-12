<?php

namespace MQM\ShopBundle\Controller\Backend\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MQM\UserBundle\Model\UserInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use MQM\UserBundle\Form\Type\StaffRegistrationType;

/**
 * @Route("/admin/registro_staff")
 */
class RegisterStaffController extends Controller {

    /**
     * @Route("/new.{_format}", defaults={"_format"="html"}, name="TKShopBackendStaffUserNew")
     * @Template("MQMShopBundle:Backend\User\RegisterStaff:new.html.twig")
     * @Method({"post", "get"})
     */
    public function newAction($_format)
    {
        $staffRegistrationFormHandler = $this->get('mqm_user.form.handler.staff_registration');
        $form = $this->createForm(new StaffRegistrationType(), null, array('validation_groups' => 'StaffRegistration'));
        $isValid = $staffRegistrationFormHandler->process($form);
        if ($isValid) {
            $user = $form->getData();
            $this->get('mqm_user.user_manager')->saveUser($user);

            return $this->redirect($this->generateUrl('TKShopBackendStaffUserClientShowAll'));
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/{id}/edit", name="TKShopBackendStaffUserEdit")
     * @Template("MQMShopBundle:Backend\User\RegisterStaff:edit.html.twig")
     * @Method({"post", "get"})
     */
    public function editAction($id)
    {
        $user = $this->get('mqm_user.user_manager')->findUserBy(array('id' => $id));
        if (!$user) {
            throw $this->createNotFoundException('Unable to find Shop\User entity.');
        }
        $form = $this->createForm(new StaffRegistrationType(), $user, array('validation_groups' => 'StaffEdition'));
        $staffRegistrationFormHandler = $this->get('mqm_user.form.handler.staff_registration');
        $isValid = $staffRegistrationFormHandler->process($form);
        if ($isValid) {
            $this->get('mqm_user.user_manager')->saveUser($user);

            return $this->redirect($this->generateUrl('TKShopBackendStaffUserClientShowAll'));
        }

        return array(
            'entity' => $user,
            'form' => $form->createView(),
        );
    }
}
