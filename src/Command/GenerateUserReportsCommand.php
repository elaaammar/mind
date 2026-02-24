<?php

namespace App\Command;

use App\Entity\Report;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:generate-user-reports',
    description: 'Generates 30 reports for the user side',
)]
class GenerateUserReportsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $titles = [
            'Audit de conformité RGPD',
            'Analyse des risques financiers',
            'Audit de sécurité réseau',
            'Évaluation de la performance RH',
            'Contrôle qualité production',
            'Audit énergétique bâtiment A',
            'Analyse de la chaîne logistique',
            'Audit flash marketing digital',
            'Évaluation de la satisfaction client',
            'Audit de la gouvernance IT',
            'Analyse des processus de vente',
            'Audit de la paie et des charges',
            'Évaluation de la stratégie RSE',
            'Audit des inventaires stocks',
            'Analyse de la cybersécurité mobile',
        ];

        $types = ['Financier', 'Sécurité', 'Opérationnel', 'Conformité', 'Qualité', 'RH'];
        $statuses = ['En cours', 'Validé', 'Terminé'];
        $priorities = ['Basse', 'Moyenne', 'Haute'];

        for ($i = 1; $i <= 30; $i++) {
            $report = new Report();
            $baseTitle = $titles[array_rand($titles)];
            $report->setTitle($baseTitle . " #" . $i);
            $report->setDescription("Ceci est une description détaillée pour l'audit généré n°" . $i . ". Analyse approfondie des processus et identification des axes d'amélioration.");
            $report->setType($types[array_rand($types)]);
            $report->setStatus($statuses[array_rand($statuses)]);
            $report->setPriority($priorities[array_rand($priorities)]);
            $report->setScore(rand(40, 95));
            $report->setSource('user');
            
            if ($report->getStatus() === 'Validé' || $report->getStatus() === 'Terminé') {
                $report->setValidationDate(new \DateTimeImmutable("-" . rand(1, 30) . " days"));
            }

            $this->entityManager->persist($report);
        }

        $this->entityManager->flush();

        $io->success('30 rapports utilisateurs ont été générés avec succès.');

        return Command::SUCCESS;
    }
}
