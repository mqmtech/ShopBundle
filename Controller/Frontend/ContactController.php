<?php

namespace MQM\ShopBundle\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/tienda/contacto")
 */
class ContactController extends Controller
{
    /**
     * @Route("/enviar_consulta", name="TKShopFrontendContactSendEmail")
     * @Method({"get", "post"})
     * @template("MQMShopBundle:Default:contacto.html.twig")
     */
    public function sendEmailAction()
    {
        $form = $this->createContactForm();        
        $request = Request::createFromGlobals();
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            $feedback = null;
            if ($form->isValid()) {
                $data = $form->getData();
                $company = $data["company"];
                $email = $data["email"];
                $message = $data["message"];
                $mailer = $this->get('mqm_shop.mailer');
                $mailer->sendEmail($email, 'amaestramientos@tecno-key.com', "[Tecnokey Online] Consulta de $company", $message);
                $feedback = $this->getFormvalidationFeedback(true);
            }
            else {
                $feedback = $this->getFormValidationFeedback(false);
            }

            $this->get('session')->getFlashBag()->set('form_feedback', $feedback);
            $this->get('session')->save();
        }
        
        return array(
            'form' => $form->createView()
        );
    }
    
    private function createContactForm($data = null, $options = array())
    {
        $form = $this->createFormBuilder($data, $options)
                ->add('company', 'text', array(
                    'required' => true,
                ))
                ->add('email', 'email', array(
                    'required' => true,
                ))
                ->add('message', 'textarea', array(
                    'required' => true,
                ))
                ->getForm();
        
        return $form;
    }
    
    /**
     * @param boolean $isSuccess
     * @return array
     */
    private function getFormValidationFeedback($isSuccess)
    {
        if (!$isSuccess) {
            return array(
            "success" => null,
            "error" => "¡Formulario incorrecto! Revise todos los campos antes de enviar.",
            "fields" => array()
            );
        }
        else {
            return array(
             "success" => "¡Su mensaje ha sido enviado con éxito!",
            );
        }
    }
}
