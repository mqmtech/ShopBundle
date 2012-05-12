<?php

namespace MQM\ShopBundle\Mailer;

use MQM\ShopBundle\Mailer\MailerInterface;

class Mailer implements MailerInterface
{    
    private $mailer;
    private $kernel;
    
    public function __construct($mailer, $kernel)
    {
        $this->mailer = $mailer;
        $this->kernel = $kernel;
    }
    
    public function sendEmail($from, $to, $subject, $body)
    {
            $currentEnvironment = $this->kernel->getEnvironment();        
            if (in_array($currentEnvironment, array('dev', 'test'))) {
                $this->sendEmailtBySwift($from, 'gdeveloperaccount@gmail.com', $subject, $body);
            }
            else {
                $this->sendEmailByPHPMail($from, $to, $subject, $body); //TODO: Uncomment this in production code
                $this->sendEmailByPHPMail($from, 'gdeveloperaccount@gmail.com', $subject, $body);
            }
    }
    
    private function sendEmailtBySwift($from, $to, $subject, $body)
    {        
            $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from)
            ->addTo($to)
            ->setBody($body);

            $this->mailer->send($message);     
    }
    
    private function sendEmailByPHPMail($from, $to, $subject, $body)
    {
        $headers = 'From: '. $from . "\r\n" .
            'Reply-To: '. $from . "\r\n" .
            'X-Mailer: PHP/' . phpversion();            
            mail($to, $subject, $body, $headers);
    }
}