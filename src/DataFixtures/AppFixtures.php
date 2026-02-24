<?php

namespace App\DataFixtures;

use App\Entity\Role;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Création des 3 rôles principaux
        
        // 1. Administrateur
        $roleAdmin = new Role();
        $roleAdmin->setNom('Administrateur');
        $roleAdmin->setDescription('Accès complet au système - Gestion des utilisateurs, rôles et configuration');
        $roleAdmin->setPermissions([
            'user.create', 'user.edit', 'user.delete', 'user.view',
            'role.create', 'role.edit', 'role.delete', 'role.view',
            'audit.create', 'audit.edit', 'audit.delete', 'audit.view', 'audit.report',
            'admin.access', 'admin.config'
        ]);
        $manager->persist($roleAdmin);

        // 2. Auditeur
        $roleAuditeur = new Role();
        $roleAuditeur->setNom('Auditeur');
        $roleAuditeur->setDescription('Responsable des audits - Peut créer, gérer et générer des rapports d\'audit');
        $roleAuditeur->setPermissions([
            'user.view',
            'role.view',
            'audit.create', 'audit.edit', 'audit.delete', 'audit.view', 'audit.report'
        ]);
        $manager->persist($roleAuditeur);

        // 3. Utilisateur
        $roleUtilisateur = new Role();
        $roleUtilisateur->setNom('Utilisateur');
        $roleUtilisateur->setDescription('Utilisateur standard - Peut consulter les audits et participer aux processus');
        $roleUtilisateur->setPermissions([
            'user.view',
            'audit.view'
        ]);
        $manager->persist($roleUtilisateur);

        // Création des utilisateurs de test
        
        // Administrateur
        $admin = new Utilisateur();
        $admin->setNom('Admin');
        $admin->setPrenom('Super');
        $admin->setEmail('admin@mindaudit.com');
        $admin->setPassword(password_hash('admin123', PASSWORD_BCRYPT));
        $admin->setRole($roleAdmin);
        $admin->setActif(true);
        $manager->persist($admin);

        // Auditeurs
        $auditeur1 = new Utilisateur();
        $auditeur1->setNom('Dupont');
        $auditeur1->setPrenom('Jean');
        $auditeur1->setEmail('jean.dupont@mindaudit.com');
        $auditeur1->setPassword(password_hash('password123', PASSWORD_BCRYPT));
        $auditeur1->setRole($roleAuditeur);
        $auditeur1->setActif(true);
        $manager->persist($auditeur1);

        $auditeur2 = new Utilisateur();
        $auditeur2->setNom('Martin');
        $auditeur2->setPrenom('Sophie');
        $auditeur2->setEmail('sophie.martin@mindaudit.com');
        $auditeur2->setPassword(password_hash('password123', PASSWORD_BCRYPT));
        $auditeur2->setRole($roleAuditeur);
        $auditeur2->setActif(true);
        $manager->persist($auditeur2);

        // Utilisateurs standards
        $utilisateur1 = new Utilisateur();
        $utilisateur1->setNom('Bernard');
        $utilisateur1->setPrenom('Pierre');
        $utilisateur1->setEmail('pierre.bernard@mindaudit.com');
        $utilisateur1->setPassword(password_hash('password123', PASSWORD_BCRYPT));
        $utilisateur1->setRole($roleUtilisateur);
        $utilisateur1->setActif(true);
        $manager->persist($utilisateur1);

        $utilisateur2 = new Utilisateur();
        $utilisateur2->setNom('Leroy');
        $utilisateur2->setPrenom('Marie');
        $utilisateur2->setEmail('marie.leroy@mindaudit.com');
        $utilisateur2->setPassword(password_hash('password123', PASSWORD_BCRYPT));
        $utilisateur2->setRole($roleUtilisateur);
        $utilisateur2->setActif(true);
        $manager->persist($utilisateur2);

        // Utilisateur inactif
        $inactif = new Utilisateur();
        $inactif->setNom('Dubois');
        $inactif->setPrenom('Luc');
        $inactif->setEmail('luc.dubois@mindaudit.com');
        $inactif->setPassword(password_hash('password123', PASSWORD_BCRYPT));
        $inactif->setRole($roleUtilisateur);
        $inactif->setActif(false);
        $manager->persist($inactif);

        // --- AJOUT DE RAPPORTS ET RECOMMANDATIONS ---
        
        // 1. Un rapport critique pour l'admin
        $report1 = new \App\Entity\Report();
        $report1->setTitle('Audit de Sécurité Infrastructure Q1');
        $report1->setDescription('Analyse approfondie des vulnérabilités serveurs et réseaux pour le premier trimestre.');
        $report1->setStatus('Critique');
        $report1->setType('Sécurité');
        $report1->setScore(45);
        $report1->setPriority('Forte');
        $report1->setSource('admin');
        $manager->persist($report1);

        $rec1 = new \App\Entity\Recommendation();
        $rec1->setContent("Mettre à jour le kernel Linux sur les serveurs de production pour corriger la faille CVE-2024-XXXX.");
        $rec1->setReport($report1);
        $rec1->setPotentialImpact(25);
        $manager->persist($rec1);

        $rec2 = new \App\Entity\Recommendation();
        $rec2->setContent("Renforcer la politique de rotation des mots de passe pour les comptes à privilèges.");
        $rec2->setReport($report1);
        $rec2->setPotentialImpact(10);
        $manager->persist($rec2);

        // 2. Un rapport validé pour l'admin
        $report2 = new \App\Entity\Report();
        $report2->setTitle('Évaluation Performance Application Mobile');
        $report2->setDescription("Rapport sur les temps de réponse et l'expérience utilisateur de l'application mobile.");
        $report2->setStatus('Validé');
        $report2->setType('Performance');
        $report2->setScore(88);
        $report2->setPriority('Moyenne');
        $report2->setSource('admin');
        $manager->persist($report2);

        $rec3 = new \App\Entity\Recommendation();
        $rec3->setContent("Optimiser le cache des images pour réduire le temps de chargement de 15%.");
        $rec3->setReport($report2);
        $rec3->setIsApplied(true);
        $manager->persist($rec3);

        // 3. Un rapport pour les utilisateurs (espace client)
        $report3 = new \App\Entity\Report();
        $report3->setTitle('Audit Conformité RGPD');
        $report3->setDescription("Vérification de la conformité des formulaires de collecte de données.");
        $report3->setStatus('En cours');
        $report3->setType('Juridique');
        $report3->setScore(62);
        $report3->setPriority('Basse');
        $report3->setSource('user');
        $manager->persist($report3);

        $rec4 = new \App\Entity\Recommendation();
        $rec4->setContent("Ajouter une case à cocher explicite pour le consentement marketing sur la page d'inscription.");
        $rec4->setReport($report3);
        $manager->persist($rec4);

        $manager->flush();
    }
}
