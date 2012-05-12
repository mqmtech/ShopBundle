<?php

namespace MQM\ShopBundle\Mailer\Handler;

use MQM\ShopBundle\Mailer\MailerInterface;
use MQM\OrderBundle\Model\OrderInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Request;

class OrderNotificator
{    
    private $mailer;
    private $router;
    private $request;

    public function __construct(MailerInterface $mailer, RouterInterface $router, Request $request)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->request = $request;

    }

    public function sendOrderPlacedNotification(OrderInterface $order)
    {
        $user = $order->getUser();
        try {
            $email = $user->getEmail() == '' || null ? $user->getFirstName() . '_' . $user->getLastName() . '@nomail.com' : $user->getEmail();
            $this->mailer->sendEmail($email, 'amaestramientos@tecno-key.com', "[Tecnokey Online][Pedido] se ha realidazo un pedido",
            'Usuario: '. $user->getFirstName() . ' ' . $user->getLastName() . '
             Pedido: ' . $order->getPublicId() . '
             Ver detalles: ' . 'http://' . $this->request->getHttpHost(). $this->router->generate('TKShopBackendOrdersShowAll') . '#' . $order->getPublicId()
            );
        }
        catch(\Exception $e) {
        }
    }
}