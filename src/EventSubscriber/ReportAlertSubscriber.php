<?php

namespace App\EventSubscriber;

use App\Entity\Alert;
use App\Entity\Report;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class ReportAlertSubscriber implements EventSubscriberInterface
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postUpdate,
        ];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->checkAndCreateAlert($args);
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->checkAndCreateAlert($args);
    }

    private function checkAndCreateAlert(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Report) {
            return;
        }

        $entityManager = $args->getObjectManager();
        $createAlert = false;
        $title = "";
        $message = "";
        $type = "info";

        // Logic 1: Low Score
        if ($entity->getScore() !== null && $entity->getScore() < 40) {
            $createAlert = true;
            $title = "alerts.crit_perf";
            $message = "Le rapport '" . $entity->getTitle() . "' présente un score alarmant de " . $entity->getScore() . "%.";
            $type = "danger";
        }

        // Logic 2: High Priority
        if ($entity->getPriority() === 'Forte') {
            $createAlert = true;
            $title = "alerts.high_priority";
            $message = "Une attention immédiate est requise pour le rapport '" . $entity->getTitle() . "'.";
            $type = "warning";
        }

        // Logic 3: Critical Status
        if ($entity->getStatus() === 'Critique') {
            $createAlert = true;
            $title = "alerts.crit_state";
            $message = "Le système a marqué le rapport '" . $entity->getTitle() . "' comme étant dans un état critique.";
            $type = "danger";
        }

        // Logic 4: Excellent Score (Positive)
        if ($entity->getScore() !== null && $entity->getScore() >= 90) {
            $createAlert = true;
            $title = "alerts.exc_score";
            $message = "Félicitations ! Le rapport '" . $entity->getTitle() . "' a atteint un score exceptionnel de " . $entity->getScore() . "%.";
            $type = "success";
        }

        if ($createAlert) {
            $alert = new Alert();
            $alert->setTitle($title);
            $alert->setMessage($message);
            $alert->setType($type);
            $alert->setReport($entity);

            $entityManager->persist($alert);
            $entityManager->flush();
        }
    }
}
