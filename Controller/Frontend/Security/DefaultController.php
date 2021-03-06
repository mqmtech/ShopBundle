<?php

namespace MQM\ShopBundle\Controller\Frontend\Security;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Frontend\Default controller.
 *
 * @Route("/tienda/seguridad")
 */
class DefaultController extends Controller
{    
    /**
     * @Route("/login", name="TKShopFrontendSecurityLogin")
     */
    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render('MQMShopBundle:Frontend\Security\Default:login.html.twig', array(
            // last username entered by the user
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        ));
    }
    
    /**
     *
     * @Route("/auto_login.{_format}", defaults={"_format"="html"}, name="TKShopFrontendSecurityAutoLogin")
     */
    public function autoLoginAction()
    {
        $user = $this->get('mqm_user.user_manager')->findUserBy(array('id' => 1));

        // create the authentication token
        $token = new UsernamePasswordToken(
                        $user,
                        null,
                        'main',
                        $user->getRoles());
        // give it to the security context
        $this->container->get('security.context')->setToken($token);

        return $this->redirect($this->generateUrl("TKShopFrontendIndex"));
    }
    
    /**
     * @Route("/login_check", name="TKShopFrontendSecurityLoginCheck")
     */
    public function securityCheckAction()
    {
        // The security layer will intercept this request
        // get the login error if there is one
        $request = $this->getRequest();
        $session = $request->getSession();
        $session->getBag('security_error')->set('security_error',array(
            'label' => 'Error en usuario o contraseña',
            'description' => 'Si ya se ha registrado puede ser debido a que su cuenta esté pendiente de validar'
        ));
        $this->get('session')->save();
    }
    
    /**
     * @Route("/logout", name="TKShopFrontendSecurityLogout")
     */
    public function securityLogoutAction()
    {
        // The security layer will intercept this request
    }
}
