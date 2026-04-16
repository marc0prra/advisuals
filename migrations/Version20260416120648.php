<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260416120648 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('DROP INDEX UNIQ_880E0D76E7927C74 ON admin');
        $this->addSql('ALTER TABLE admin DROP name, DROP created_at, CHANGE email email VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE estimate_request DROP FOREIGN KEY `FK_262B3365ED5CA9E6`');
        $this->addSql('DROP INDEX IDX_262B3365ED5CA9E6 ON estimate_request');
        $this->addSql('ALTER TABLE estimate_request CHANGE client_phone client_phone VARCHAR(10) NOT NULL, CHANGE message message LONGTEXT NOT NULL, CHANGE status status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE project CHANGE category category VARCHAR(255) NOT NULL, CHANGE image_path image_path VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE revenue_tracking DROP FOREIGN KEY `FK_DDD80AB8708A50AD`');
        $this->addSql('DROP INDEX IDX_DDD80AB8708A50AD ON revenue_tracking');
        $this->addSql('ALTER TABLE revenue_tracking CHANGE estimate_request_id estimate_id INT NOT NULL');
        $this->addSql('ALTER TABLE service CHANGE category category VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE admin ADD name VARCHAR(100) DEFAULT NULL, ADD created_at DATETIME NOT NULL, CHANGE email email VARCHAR(180) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_880E0D76E7927C74 ON admin (email)');
        $this->addSql('ALTER TABLE estimate_request CHANGE client_phone client_phone VARCHAR(20) DEFAULT NULL, CHANGE message message LONGTEXT DEFAULT NULL, CHANGE status status VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE estimate_request ADD CONSTRAINT `FK_262B3365ED5CA9E6` FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('CREATE INDEX IDX_262B3365ED5CA9E6 ON estimate_request (service_id)');
        $this->addSql('ALTER TABLE project CHANGE category category VARCHAR(100) NOT NULL, CHANGE image_path image_path VARCHAR(500) NOT NULL');
        $this->addSql('ALTER TABLE revenue_tracking CHANGE estimate_id estimate_request_id INT NOT NULL');
        $this->addSql('ALTER TABLE revenue_tracking ADD CONSTRAINT `FK_DDD80AB8708A50AD` FOREIGN KEY (estimate_request_id) REFERENCES estimate_request (id)');
        $this->addSql('CREATE INDEX IDX_DDD80AB8708A50AD ON revenue_tracking (estimate_request_id)');
        $this->addSql('ALTER TABLE service CHANGE category category VARCHAR(100) NOT NULL');
    }
}
