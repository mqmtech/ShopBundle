<?php

namespace MQM\ShopBundle\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use MQM\NewsLetterBundle\Model\SubscriberManagerInterface;
use MQM\NewsLetterBundle\Model\NewsLetterManagerInterface;
use MQM\NewsLetterBundle\NewsLetter\NewsLetterAdministrator;
use MQM\ShopBundle\Form\Type\NewsLetterType;

/**
 * @Route("/admin/newsletter")
 */
class NewsLetterController extends Controller
{
    /**
     * @var NewsLetterManagerInterface
     */
    private $newsLetterManager;

    /**
     * @var SubscriberManagerInterface
     */
    private $subscriberManager;

    /**
     *
     * @var NewsLetterAdministrator
     */
    private $newsLetterAdmin;

    /**
     * @Route("/nuevo", name="TKShopBackendNewsLetterNew")
     * @Method("get")
     * @Template()
     */
    public function newAction()
    {
        $newsLetterManager = $this->get('mqm_newsLetter.newsLetter_manager');
        $newsLetter = $newsLetterManager->createNewsLetter();
        $form = $this->createForm(new NewsLetterType(), $newsLetter);
        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/editar", name="TKShopBackendNewsLetterEdit")
     * @Method("post")
     * @Template("MQMShopBundle:Backend\NewsLetter:new.html.twig")
     */
    public function editAction()
    {
        $newsLetterManager = $this->get('mqm_newsLetter.newsLetter_manager');
        $newsLetter = $newsLetterManager->createNewsLetter();
        $form = $this->createForm(new NewsLetterType(), $newsLetter);

        $request = $this->getRequest();
        $form->bindRequest($request);

        return array(
                'form' => $form->createView(),
                'newsLetter' => $newsLetter
            );
    }

    /**
     * @Route("/preview", name="TKShopBackendNewsLetterPreview")
     * @Method("post")
     * @Template("MQMShopBundle:Backend\NewsLetter:new.html.twig")
     */
    public function previewAction()
    {
        $newsLetterManager = $this->get('mqm_newsLetter.newsLetter_manager');
        $newsLetter = $newsLetterManager->createNewsLetter();
        $form = $this->createForm(new NewsLetterType(), $newsLetter);

        $request = $this->getRequest();
        $form->bindRequest($request);

        if ($form->isValid()) {
            return $this->render('MQMShopBundle:Backend\NewsLetter:preview.html.twig',
            array(
                'form' => $form->createView(),
                'newsLetter' => $newsLetter
            ));
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/create", name="TKShopBackendNewsLetterCreate")
     * @Method("post")
     * @Template("MQMShopBundle:Backend\NewsLetter:new.html.twig")
     */
    public function createNewsLetterAction()
    {
        $newsLetterManager = $this->get('mqm_newsLetter.newsLetter_manager');
        $newsLetter = $newsLetterManager->createNewsLetter();
        $form = $this->createForm(new NewsLetterType(), $newsLetter);

        $request = $this->getRequest();
        $form->bindRequest($request);

        try {
            if ($form->isValid()) {
                $newsLetterAdmin = $this->get('mqm_newsLetter.newsLetter_administrator');
                $newsLetterAdmin->sendNewsLetter($newsLetter);

                //return $this->redirect($this->generateUrl('TKShopBackendIndex'));
                return $this->render('MQMShopBundle:Backend\NewsLetter:success.html.twig');
            }
        }catch(\Exception $e) {
            $this->get('session')->getFlashBag()->set('NewsLetter', "Error procesando el envio de NewsLetter");
            $this->get('session')->save();
        }

        return array(
            'form' => $form->createView(),
            'newsLetter' => $newsLetter
        );
    }

    /**
     * @Route("/enviar", name="TKShopBackendNewsLetterSend")
     * @Method("post")
     */
    public function sendAction()
    {
        $this->createNewSubscriber();
        $newsLetter = $this->createNewLetter();
        $this->newsLetterAdmin = $this->get('mqm_newsLetter.newsLetter_administrator');
        $this->newsLetterAdmin->sendNewsLetter($newsLetter);

        return $this->redirect($this->generateUrl('TKShopBackendNewsLetterIndex'));
    }

    private function createNewLetter()
    {
        $this->newsLetterManager = $this->get('mqm_newsLetter.newsLetter_manager');

        $newsLetter = $this->newsLetterManager->createNewsLetter();
        $newsLetter->setBody('test newsletter body');
        $newsLetter->setTitle('test newsletter');

        return $newsLetter;
    }

    private function createNewSubscriber()
    {
        $this->subscriberManager = $this->get('mqm_newsLetter.subscriber_manager');
        $subscriber = $this->subscriberManager->createSubscriber();
        $subscriber->setEmail('ciberxtrem@gmail.com');
        $this->subscriberManager->saveSubscriber($subscriber, true);

        return $subscriber;
    }
}
