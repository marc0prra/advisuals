<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260419100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout des entités de gestion de contenu : testimonial, skill, approach_step, practical_info, site_settings. Mise à jour de project et service.';
    }

    public function up(Schema $schema): void
    {
        // Testimonials
        $this->addSql('CREATE TABLE testimonial (
            id INT AUTO_INCREMENT NOT NULL,
            author VARCHAR(255) NOT NULL,
            role VARCHAR(255) NOT NULL,
            quote LONGTEXT NOT NULL,
            rating INT NOT NULL DEFAULT 5,
            is_featured TINYINT(1) NOT NULL DEFAULT 0,
            featured_position INT DEFAULT NULL,
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

        // Skills (compétences — page about)
        $this->addSql('CREATE TABLE skill (
            id INT AUTO_INCREMENT NOT NULL,
            title VARCHAR(255) NOT NULL,
            description LONGTEXT NOT NULL,
            position INT NOT NULL DEFAULT 0,
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

        // Approach steps (méthode — page about)
        $this->addSql('CREATE TABLE approach_step (
            id INT AUTO_INCREMENT NOT NULL,
            title VARCHAR(255) NOT NULL,
            description LONGTEXT NOT NULL,
            position INT NOT NULL DEFAULT 0,
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

        // Practical info (services page)
        $this->addSql('CREATE TABLE practical_info (
            id INT AUTO_INCREMENT NOT NULL,
            label VARCHAR(100) NOT NULL,
            title VARCHAR(255) NOT NULL,
            description LONGTEXT NOT NULL,
            position INT NOT NULL DEFAULT 0,
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

        // Site settings (singleton — contenu about + home about block)
        $this->addSql('CREATE TABLE site_settings (
            id INT AUTO_INCREMENT NOT NULL,
            about_bio_intro LONGTEXT NOT NULL,
            about_bio1 LONGTEXT NOT NULL,
            about_bio2 LONGTEXT NOT NULL,
            about_quote VARCHAR(500) NOT NULL,
            about_portrait_path VARCHAR(500) NOT NULL,
            home_about_title VARCHAR(255) NOT NULL,
            home_about_text1 LONGTEXT NOT NULL,
            home_about_text2 LONGTEXT NOT NULL,
            home_about_image_path VARCHAR(500) NOT NULL,
            home_about_quote VARCHAR(500) NOT NULL,
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

        // Project — nouvelles colonnes
        $this->addSql('ALTER TABLE project
            ADD description LONGTEXT DEFAULT NULL,
            ADD featured_position INT DEFAULT NULL,
            ADD featured_size VARCHAR(20) DEFAULT \'normal\'
        ');

        // Données par défaut pour 3 témoignages
        $this->addSql("INSERT INTO testimonial (author, role, quote, rating, is_featured, featured_position) VALUES
            ('Thomas R.', 'Directeur Artistique, Agence R&Co', 'Adrien a capturé notre campagne avec une justesse rare. Chaque image est un argument de vente à elle seule.', 5, 1, 1),
            ('Clara F.', 'Rédactrice en chef, Vogue Paris', 'Le meilleur photographe avec qui j\\'ai travaillé. Une vision artistique forte et des résultats qui dépassent systématiquement les attentes.', 5, 1, 2),
            ('Sophie M.', 'CEO, Agence Paris', 'Mon portrait corporate a transformé ma présence LinkedIn. Adrien sait créer une image qui reflète exactement qui vous êtes.', 5, 1, 3)
        ");

        // Données par défaut pour les compétences
        $this->addSql("INSERT INTO skill (title, description, position) VALUES
            ('Formation Autodidacte', 'Parcours atypique enrichi par une acceptation dans une grande école de photographie française. L\\'auto-formation comme moteur d\\'excellence.', 0),
            ('Projets Prestigieux', 'Collaborations avec des marques internationales — Adidas, Lacoste, publications presse. Portrait authentique et direction artistique exigeante.', 1),
            ('Polyvalence', 'De la photo de presse aux portraits commerciaux — une adaptabilité totale à tous types de projets sans jamais sacrifier l\\'exigence.', 2)
        ");

        // Données par défaut pour les étapes d'approche
        $this->addSql("INSERT INTO approach_step (title, description, position) VALUES
            ('Écoute & Compréhension', 'Je prends le temps d\\'analyser votre projet pour proposer une approche sur mesure. De la discussion initiale au brief final, chaque détail compte.', 0),
            ('Préparation Méticuleuse', 'Repérage des lieux, choix du matériel et planification de la lumière — rien n\\'est laissé au hasard pour garantir la qualité finale.', 1),
            ('Livraison Sans Compromis', 'Post-production soignée, galerie privée en ligne, fichiers haute résolution. Vous recevez un travail fini, pas des bruts à trier.', 2)
        ");

        // Données par défaut pour les infos pratiques
        $this->addSql("INSERT INTO practical_info (label, title, description, position) VALUES
            ('Zone', 'Paris & France entière', 'Basé à Paris. Déplacements partout en France selon les projets. International sur demande.', 0),
            ('Délais', '7 à 21 jours', 'Portraits : 7-14 jours. Événements : 3 semaines. Livraison express disponible sur demande.', 1),
            ('Paiement', 'Acompte 30%', '30% à la réservation, solde à la livraison. Virement bancaire ou PayPal acceptés.', 2),
            ('Matériel', 'Équipement pro redondant', 'Boîtiers haut de gamme en double exemplaire pour garantir la sécurité de vos images.', 3)
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE testimonial');
        $this->addSql('DROP TABLE skill');
        $this->addSql('DROP TABLE approach_step');
        $this->addSql('DROP TABLE practical_info');
        $this->addSql('DROP TABLE site_settings');
        $this->addSql('ALTER TABLE project DROP description, DROP featured_position, DROP featured_size');
    }
}
