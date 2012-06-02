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

/**
 * @Route("/admin/registro_clientes")
 */
class RegisterClientController extends Controller {

    /**
     *
     * @Route("/new.{_format}", defaults={"_format"="html"}, name="TKShopBackendUserNew")
     * @Template()
     */
    public function newAction($_format) {

        $entity = $this->get('mqm_user.user_manager')->createUser();

        $form = $this->createForm(new UserType(), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView()
        );
    }

    /**
     * Creates a new Shop\User entity.
     *
     * @Route("/test", name="TKShopBackendUserTest")
     * @Method("get")
     * @Template("MQMShopBundle:Backend\User\Register:successMessageOnCreate.html.twig")
     */
    public function testAction() {
        return array();
    }

    /**
     * Creates a new Shop\User entity.
     *
     * @Route("/create", name="TKShopBackendUserCreate")
     * @Method("post")
     * @Template("MQMShopBundle:Backend\User\RegisterClient:new.html.twig")
     */
    public function createAction()
    {
        $userManager = $this->get('mqm_user.user_manager');
        $entity = $userManager->createUser();
        $request = $this->getRequest();
        $form = $this->createForm(new UserType(), $entity);
        $form->bindRequest($request);
        
        $basicValidation = $form->isValid();
        $extraValidation = $this->extraRegistrationValidation($entity);        
        if ($basicValidation && $extraValidation) {
            // encode password //
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($entity);
            $password = $encoder->encodePassword($entity->getPassword(), $entity->getSalt());
            $entity->setPassword($password);
            // end encoding password //
            if ($entity->getUsername() == null || $entity->getUsername() == '') {
                $entity->setUsername($entity->getEmail());
            }
            
            $entity->setIsEnabled(true);
            
            $userManager->saveUser($entity);
            $this->postProcessUserCreation($entity);
            
            return $this->redirect($this->generateUrl('TKShopBackendUserClientShowAll'));
        }
        
        return array(
            'entity' => $entity,
            'form' => $form->createView()
        );
    }

    private function postProcessUserCreation(UserInterface $user)
    {
        $this->get('mqm_shop.user_notificator')->sendUserRegistrationMessage($user);
    }

    /**
     * Displays a form to edit an existing Shop\User entity.
     *
     * @Route("/{id}/edit", name="TKShopBackendUserEdit")
     * @Template("MQMShopBundle:Backend\User\RegisterClient:edit.html.twig")
     */
    public function editAction($id)
    {
        $entity = $this->get('mqm_user.user_manager')->findUserBy(array('id' => $id));
        $entity->setPassword('');
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Shop\User entity.');
        }
        $editForm = $this->createClientUserForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * @Route("/{id}/update", name="TKShopBackendUserUpdate")
     * @Method("post")
     * @Template("MQMShopBundle:Backend\User\RegisterClient:edit.html.twig")
     */
    public function updateAction($id)
    {
        $userManager = $this->get('mqm_user.user_manager');
        $entity = $userManager->findUserBy(array('id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Shop\User entity.');
        }
        $editForm = $this->createClientUserForm($entity);
        $deleteForm = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $password = $entity->getPassword();
        $editForm->bindRequest($request);
        if ($editForm->isValid()) {
            $newPassword = $entity->getPassword();
            if ($newPassword!=null && count(trim($newPassword)) > 0) {
                $this->encodeUserPassword($entity);
            }
            else {
                $entity->setPassword($password);
            }
            $userManager->saveUser($entity);
            return $this->redirect($this->generateUrl('TKShopBackendUserClientShowAll'));
        }

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    
    private function createClientUserForm(UserInterface $user)
    {
        $form = $this->createForm(new UserType(), $user, array('intention' => 'Revision', 'validation_groups' => 'Revision'));
        
        return $form;
    }
    
    private function encodeUserPassword(UserInterface $user)
    {
        $factory = $this->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
        $user->setPassword($password);
        
        return $password;
    }

    public function extraRegistrationValidation($entity) {
        
        $validation = true;
        
        //Validate mail and passwords
        if (!$this->validateEmail($entity)) {
            $this->get('session')->getFlashBag()->set('Email', "No coincide en las dos casillas");
            $validation = false;
        }
        if (!$this->validatePassword($entity)) {
            $this->get('session')->getFlashBag()->set('Contraseña', "No coincide en las dos casillas");
            $validation = false;
        }

        $this->get('session')->save();
        //End validating mail and passwords
        
        return $validation;
    }

    private function validateCheckbox() {
        $request = Request::createFromGlobals();
        $hasCheckbox = $request->request->has('checkbox');

        return $hasCheckbox;
    }

    private function validateEmail(User $entity) {
        $request = Request::createFromGlobals();
        $email = $request->request->get('emailConfirmation');

        if ($entity->getEmail() != $email) {
            return false;
        }

        return true;
    }

    private function validatePassword(User $entity) {
        $request = Request::createFromGlobals();
        $password = $request->request->get('passwordConfirmation');

        if ($entity->getPassword() != $password) {
            return false;
        }
        return true;
    }

    private function createDeleteForm($id) {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm()
        ;
    }
}
