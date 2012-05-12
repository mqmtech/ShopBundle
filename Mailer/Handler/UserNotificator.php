<?php

namespace MQM\ShopBundle\Mailer\Handler;

use MQM\ShopBundle\Mailer\MailerInterface;
use MQM\UserBundle\Model\UserInterface;

class UserNotificator
{    
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendUserRegistrationMessage(UserInterface $user)
    {
        try {
            $this->mailer->sendEmail('amaestramientos@tecno-key.com', $user->getEmail(), "[Tecnokey] Registro recibido",
            'Gracias '. $user->getFirstName() . ' ' . $user->getLastName() . ' por completar su registro en Tecnokey, cuando su cuenta sea validada le enviaremos un e-mail de confirmación.');
        }
        catch(\Exception $e) {
        }
    }
    
    public function sendUserValidationMessage(UserInterface $user)
    {
        try {
            $this->mailer->sendEmail('amaestramientos@tecno-key.com', $user->getEmail(), "[Tecnokey] Registro finalizado",
            'Le informamos que el registro de ' . $user->getFirstName() . ' ' . $user->getLastName() . ' ha sido validado.
            Ya puede dirigirse a www.tecno-key.com/tienda/usuario/ver para ingresar su email y contraseña.
            Desde ese momento podrá ver toda la información del catálogo y realizar pedidos.
            ');
        }
        catch(\Exception $e) {
        }
    }

}