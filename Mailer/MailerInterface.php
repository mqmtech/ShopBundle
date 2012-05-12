<?php

namespace MQM\ShopBundle\Mailer;

interface MailerInterface
{    
    public function sendEmail($from, $to, $subject, $body);
}