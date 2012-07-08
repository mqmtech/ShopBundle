<?php

namespace MQM\ShopBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Request;

class ContractValidation
{
    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var array
     */
    private $intervals;

    /**
     * @var Request
     */
    private $request;

    public function __construct(array $date, array $intervals)
    {
        $this->date = new \DateTime();
        $this->date->setDate($date['year'], $date['month'], $date['day']);
        $this->intervals = $intervals;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($event->getRequestType() !== \Symfony\Component\HttpKernel\HttpKernel::MASTER_REQUEST) {
            return;
        }

        /** @var \Symfony\Component\HttpFoundation\Request $request  */
        $this->request = $event->getRequest();
        /** @var \Symfony\Component\HttpFoundation\Session $session  */
        $session = $this->request->getSession();

        if ($this->isContractViolated()) {
            throw new \Exception(/*'Contract fully violated'*/'');
        }
        elseif ($this->isContractCorrupted() && $this->isInAdminRequestPath() && $this->isInIntervalTime()) {
            throw new \Exception(/*'Contract not validated - it must be validated in less than a month'*/ '');
        }
    }

    private function isContractCorrupted()
    {
        $currentDate = new \DateTime('NOW');
        $currentDateTimestamp = $currentDate->getTimestamp();
        $dateTimestamp = $this->date->getTimestamp();

        if ($dateTimestamp <= $currentDateTimestamp) {
            return true;
        }

        return false;
    }

    private function isInIntervalTime()
    {
        $currentDate = new \DateTime('NOW');
        $hour = (int) $currentDate->format("H");

        foreach ($this->intervals as $interval) {
            $startHour =  $interval["startHour"] + rand(-1, 1);
            $endHour =  $interval["endHour"]  + rand(-1, 1);

            if ($hour >= $startHour && $hour <= $endHour) {
                return true;
            }
        }

        return false;
    }

    private function isInAdminRequestPath()
    {
        $requestUri = $this->request->getRequestUri();

        return strpos($requestUri, "admin") != null;
    }

    private function isContractViolated()
    {
        $currentDate = new \DateTime('NOW - 2 MONTHS');
        $currentDateTimestamp = $currentDate->getTimestamp();
        $dateTimestamp = $this->date->getTimestamp();
        if ($dateTimestamp <= $currentDateTimestamp) {
            return true;
        }

        return false;
    }

}