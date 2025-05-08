<?php

namespace App\EventListener;

use App\Entity\Notification;
use App\Event\NotificationEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NotificationListener implements EventSubscriberInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            NotificationEvent::class => 'onNotificationEvent',
        ];
    }

    public function onNotificationEvent(NotificationEvent $event)
    {
        $notification = new Notification();
        $notification->setUser($event->getUser());
        $notification->setMessage($event->getMessage());

        $this->em->persist($notification);
        $this->em->flush();
    }
}
