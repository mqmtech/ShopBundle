<?php

namespace MQM\ShopBundle\Controller\Frontend\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/tienda/newsletter")
 */
class NewsLetterController extends Controller {

    /**
     * @Route("/subcripcion.{_format}", defaults={"_format"="partialhtml"}, name="TKShopFrontendNewsLetterSubscription")
     * @Template()
     */
    public function subscriptionAction($_format)
    {
        if ($this->isCurrentUserSubscribed()) {
            return $this->render("MQMShopBundle:Frontend\User\NewsLetter:unsubscribe.".$_format.".twig");
        }
        else {
            return $this->render("MQMShopBundle:Frontend\User\NewsLetter:subscribe.".$_format.".twig");
        }
    }

    /**
     * @Route("/subcribe.{_format}", defaults={"_format"="partialhtml"}, name="TKShopFrontendNewsLetterSubscribe")
     * @Template()
     */
    public function subscribeAction($_format)
    {
        if ($this->isCurrentUserSubscribed()) {
            return $this->redirect($this->generateUrl('TKShopFrontendUserShow'));
        }
        $user = $this->getCurrentUser();
        $subscriberManager = $this->get('mqm_newsLetter.subscriber_manager');

        $subscriber = $subscriberManager->createSubscriber();
        $subscriber->setEmail($user->getEmail());
        $subscriber->setName($user->getFirstName());
        $subscriberManager->saveSubscriber($subscriber, true);

        return $this->redirect($this->generateUrl('TKShopFrontendUserShow'));
    }

    /**
     * @Route("/unsubcribe.{_format}", defaults={"_format"="partialhtml"}, name="TKShopFrontendNewsLetterUnsubscribe")
     * @Template()
     */
    public function unsubscribeAction($_format)
    {
        if (!$this->isCurrentUserSubscribed()) {
            return $this->redirect($this->generateUrl('TKShopFrontendUserShow'));
        }
        $user = $this->getCurrentUser();
        $subscriberManager = $this->get('mqm_newsLetter.subscriber_manager');
        $subscriber = $subscriberManager->findSubscriberBy( array(
                'email' => $user->getEmail(),
            )
        );

        if ($subscriber != null) {
            $subscriberManager->deleteSubscriber($subscriber, true);
        }

        return $this->redirect($this->generateUrl('TKShopFrontendUserShow'));
    }

    private function isCurrentUserSubscribed()
    {
        $user = $this->getCurrentUser();
        if ($user == null) {
            $this->createNotFoundException('Usuario no registrado');
        }

        $subscriberManager = $this->get('mqm_newsLetter.subscriber_manager');
        $subscriber = $subscriberManager->findSubscriberBy( array(
            'email' => $user->getEmail(),
            )
        );

        if ($subscriber == null || count($subscriber) < 1) {
            return false;
        }

        return true;
    }

    public function getCurrentUser()
    {
        return $this->get('mqm_user.user_resolver')->getCurrentUser();
    }
}
