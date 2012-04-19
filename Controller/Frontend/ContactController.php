<?php

namespace MQM\ShopBundle\Controller\Frontend;

use MQM\BrandBundle\Entity\Brand;
use MQM\ProductBundle\Entity\Product;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Exception;

/**
 * Frontend\Default controller.
 *
 * @Route("/tienda/contacto")
 */
class ContactController extends Controller {

    /**
     *
     * @Route("/", name="TKShopFrontendContactIndex")
     * @Template("MQMShopBundle:Default:contacto.html.twig")
     */
    public function indexAction() {
        
        $form = $this->createContactForm();
        
        return array(
            'form' => $form->createView(),
        );
    }
    
    /**
     *
     * @Route("/enviar_consulta", name="TKShopFrontendContactSendEmail")
     * @Method("post")
     * @template("MQMShopBundle:Default:contacto.html.twig")
     */
    public function sendEmailAction() {
        $form = $this->createContactForm();
        
        $request = Request::createFromGlobals();        
        $form->bindRequest($request);
        $feedback = null;
        if($form->isValid()){
            $data = $form->getData();
        
            $company = $data["company"];
            $email = $data["email"];
            $message = $data["message"];
            
            $mailer = $this->get('mqm_shop.mailer');
            $mailer->sendEmail($email, 'amaestramientos@tecno-key.com', "[Tecnokey Online] Consulta de $company", $message);
            
            $feedback = $this->getFormvalidationFeedback(true);
        }
        else{
            $feedback = $this->getFormvalidationFeedback(false);
        }
            $this->get('session')->setFlash('form_feedback', $feedback);
        
        return array('form' => $form->createView());
    }
    
    public function createContactForm($data = null, $options = array()){
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
     *
     * @param boolean $isSuccess
     * @return type 
     */
    public function getFormvalidationFeedback($isSuccess){

        if(!$isSuccess) {
            return array(
            "success" => null,
            "error" => "¡Formulario incorrecto! Revise todos los campos antes de enviar.",
            "fields" => array()
            );
        }
        else{
            return array(
             "success" => "¡Su mensaje ha sido enviado con éxito!",
            );
        }
    }
}
