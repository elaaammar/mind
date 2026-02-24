<?php

namespace App\EventSubscriber;

use App\Repository\ReportRepository;
use CalendarBundle\Entity\Event;
use CalendarBundle\CalendarEvents;
use CalendarBundle\Event\CalendarEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CalendarSubscriber implements EventSubscriberInterface
{
    private $reportRepository;
    private $router;

    public function __construct(
        ReportRepository $reportRepository,
        UrlGeneratorInterface $router
    ) {
        $this->reportRepository = $reportRepository;
        $this->router = $router;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CalendarEvents::SET_DATA => 'onCalendarSetData',
        ];
    }

    public function onCalendarSetData(CalendarEvent $calendarEvent)
    {
        $start = $calendarEvent->getStart();
        $end = $calendarEvent->getEnd();
        $filters = $calendarEvent->getFilters();
        
        $isUser = is_array($filters) && isset($filters['is_user']) && $filters['is_user'];

        // Query the reports based on the dates
        $qb = $this->reportRepository->createQueryBuilder('r')
            ->where('r.createdAt BETWEEN :start AND :end')
            ->setParameter('start', $start->format('Y-m-d H:i:s'))
            ->setParameter('end', $end->format('Y-m-d H:i:s'));

        if ($isUser) {
            $qb->andWhere('r.source = :source')->setParameter('source', 'user');
        }

        $reports = $qb->getQuery()->getResult();

        foreach ($reports as $report) {
            // Create the event
            $reportEvent = new Event(
                (string) $report->getTitle(),
                $report->getCreatedAt()
            );

            // Determine color
            $color = '#4e73df';
            if ($report->getStatus() === 'Critique' || $report->getPriority() === 'Haute') {
                $color = '#e74a3b';
            } elseif ($report->getStatus() === 'Validé') {
                $color = '#1cc88a';
            }

            $reportEvent->setOptions([
                'backgroundColor' => $color,
                'borderColor' => '#ffffff',
            ]);

            // Determine route
            $route = ($isUser || $report->getSource() === 'user') ? 'app_user_report_show' : 'app_report_show';

            $reportEvent->addOption(
                'url',
                $this->router->generate($route, [
                    'id' => $report->getId(),
                ])
            );

            $calendarEvent->addEvent($reportEvent);
        }
    }
}
