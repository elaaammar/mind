<?php

namespace App\EventSubscriber;

use App\Entity\Report;
use App\Entity\ReportVersion;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

#[AsDoctrineListener(event: Events::postPersist)]
#[AsDoctrineListener(event: Events::preUpdate)]
#[AsDoctrineListener(event: Events::postFlush)]
class ReportVersionSubscriber
{
    /** @var ReportVersion[] */
    private array $pendingVersions = [];

    public function __construct(
        private readonly Security $security
    ) {}

    public function postPersist(PostPersistEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof Report) {
            return;
        }
        $this->scheduleVersion($entity, 'create', [
            'title'  => $entity->getTitle(),
            'status' => $entity->getStatus(),
            'score'  => $entity->getScore(),
        ]);
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof Report) {
            return;
        }

        $tracked = ['title', 'status', 'description', 'score', 'priority'];
        $changes = [];
        foreach ($tracked as $field) {
            if ($args->hasChangedField($field)) {
                $changes[$field] = [
                    'old' => $args->getOldValue($field),
                    'new' => $args->getNewValue($field),
                ];
            }
        }

        if (!empty($changes)) {
            $this->scheduleVersion($entity, 'update', $changes);
        }
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        if (empty($this->pendingVersions)) {
            return;
        }

        $em = $args->getObjectManager();
        $versionsToSave = $this->pendingVersions;
        // Clear BEFORE flushing to avoid infinite loop
        $this->pendingVersions = [];

        foreach ($versionsToSave as $version) {
            // Calculate version number
            $count = $em->getRepository(ReportVersion::class)
                ->count(['report' => $version->getReport()]);
            $version->setVersion($count + 1);
            $em->persist($version);
        }

        $em->flush();
    }

    private function scheduleVersion(Report $report, string $action, array $data): void
    {
        $user = $this->security->getUser();

        $version = new ReportVersion();
        $version->setReport($report);
        $version->setAction($action);
        $version->setData($data);
        $version->setLoggedAt(new \DateTimeImmutable());
        $version->setUsername($user ? $user->getUserIdentifier() : 'système');

        $this->pendingVersions[] = $version;
    }
}
