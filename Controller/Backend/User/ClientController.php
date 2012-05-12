<?php

namespace MQM\ShopBundle\Controller\Backend\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use MQM\UserBundle\Model\UserManagerInterface;
use MQM\UserBundle\Model\UserInterface;
use MQM\ShopBundle\Form\Type\UserValidationType;

/**
 * @Route("/admin/usuarios/clientes")
 */
class ClientController extends Controller
{    
    /**
     * @Route("/", name="TKShopBackendUserClientIndex")
     * @Template()
     */
    public function indexAction()
    {
        return array('name' => "Usuario!");
    }    
    
    /**
     * @Route("/recientes.{_format}", defaults={"_format"="partialhtml"}, name="TKShopBackendUserClientRecent")
     * @Template()
     */
    public function recentClientsAction($_format)
    {
        $clients = $this->get('mqm_user.user_manager')->findRecentUsers();        
        return $this->render("MQMShopBundle:Backend\User\Client:recentClients.".$_format.".twig", array('clients' => $clients));
    }

    /**
     * @Route("/validacion.{_format}", defaults={"_format"="html"}, name="TKShopBackendUserClientValidation")
     * @Template()
     */
    public function validationAction($_format)
    {
        $clients = $this->get('mqm_user.user_manager')->findUsersBy(array(
            'isEnabled' => false,
            'permissionType' => 'ROLE_USER'
        ));
        $forms = $this->createValidationFormsFromClients($clients);
        $sortManager = $this->createSortManager();

        return array(
            'clients' => $clients,
            'clientForms' => $forms,
            'sortManager' => $sortManager,
        );
    }

    /**
     * @Route("/{id}/actualizar_usuario", name="TKShopBackendUserClientUpdate")
     * @Method("post")
     */
    public function updateUserAction($id)
    {
        $user = $this->getUserManager()->findUserBy(array('id' => $id));
        $editForm   = $this->createForm(new UserValidationType(), $user);
        $request = $this->getRequest();
        $editForm->bindRequest($request);
        if ($editForm->isValid()) {
            $this->getUserManager()->saveUser($user);
            $this->postProcessUserUpdate($user);

            return $this->redirect($this->generateUrl('TKShopBackendUserClientValidation'));
        }

        throw new \Exception('Invalid Brand DiscountRule');
    }

    private function postProcessUserUpdate(UserInterface $user)
    {
        $this->get('mqm_shop.user_notificator')->sendUserValidationMessage($user);
    }

    /**
     * @Route("/eliminar_cliente{id}.{_format}", defaults={"_format"="html"}, name="TKShopBackendUserClientDelete")
     * @Template()
     */
    public function deleteUserAction($id)
    {
        $user = $this->getUserManager()->findUserBy(array(
            'id' => $id
            )
        );
        $this->getUserManager()->deleteUser($user);

        return $this->redirect($this->generateUrl('TKShopBackendUserClientValidation'));
    }

    private function createValidationFormsFromClients($clients)
    {
        $forms = array();
        if ($clients != null) {
            foreach ($clients as $client) {
                $form = $this->createForm(new UserValidationType(), $client);
                $forms[] = $form->createView();
            }
        }

        return $forms;
    }

    /**
     * @return UserManagerInterface
     */
    private function getUserManager()
    {
        return $this->get('mqm_user.user_manager');
    }

    private function createSortManager()
    {
        $sortManager = $this->get('mqm_sort.sort_manager');
        $sortManager->addSort('nombre', 'username', 'Nombre')
            ->addSort('fecha', 'createdAt', 'Fecha', 'ASC', array('default' => true))
            ->addSort('estado', array('isEnabled'), 'Estado')
            ->init();

        return $sortManager;
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm();
    }
}
