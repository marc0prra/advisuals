<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260525000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout is_featured sur service + table contact_message';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE service ADD is_featured TINYINT(1) NOT NULL DEFAULT 0');

        $this->addSql('CREATE TABLE contact_message (
            id INT AUTO_INCREMENT NOT NULL,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            message LONGTEXT NOT NULL,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE service DROP COLUMN is_featured');
        $this->addSql('DROP TABLE contact_message');
    }
}
