<?php

namespace MQM\ShopBundle\Mailer;

class EcommerceMailer
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
                $this->sendEmailByPHPMail($from, $to, $subject, $body);
                $this->sendEmailByPHPMail($from, 'gdeveloperaccount@gmail.com', $subject, $body);
            }
    }
    
    public function sendEmailtBySwift($from, $to, $subject, $body)
    {        
            $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from)
            //->setTo("ciberxtrem@gmail.com")
            ->addTo($to)
            ->addTo("marioquesada85@gmail.com")
            ->setBody($body);

            $this->mailer->send($message);     
    }
    
    public function sendEmailByPHPMail($from, $to, $subject, $body)
    {
        $headers = 'From: '. $from . "\r\n" .
            'Reply-To: '. $from . "\r\n" .
            'X-Mailer: PHP/' . phpversion();            
            mail($to, $subject, $body, $headers);
    }
}