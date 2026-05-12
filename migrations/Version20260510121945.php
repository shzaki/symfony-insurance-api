<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260510121945 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comparison_request (id INT AUTO_INCREMENT NOT NULL, zipcode VARCHAR(20) NOT NULL, building_year INT NOT NULL, living_area INT NOT NULL, building_type VARCHAR(50) NOT NULL, has_garage TINYINT NOT NULL, has_solar_panels TINYINT NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE comparison_result (id INT AUTO_INCREMENT NOT NULL, monthly_price NUMERIC(10, 2) NOT NULL, yearly_price NUMERIC(10, 2) DEFAULT NULL, ranking_score INT NOT NULL, risk_level VARCHAR(20) NOT NULL, recommendation_reason LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, comparison_request_id INT NOT NULL, tariff_id INT NOT NULL, INDEX IDX_16191CA433F2B65F (comparison_request_id), INDEX IDX_16191CA492348FD2 (tariff_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE insurance_product (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(50) NOT NULL, description LONGTEXT DEFAULT NULL, is_active TINYINT NOT NULL, provider_id INT NOT NULL, INDEX IDX_3E6B2108A53A8AA (provider_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE insurance_provider (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(50) NOT NULL, is_active TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tariff (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, monthly_price NUMERIC(10, 2) NOT NULL, coverage_amount INT NOT NULL, deductible INT DEFAULT NULL, score INT NOT NULL, is_active TINYINT NOT NULL, product_id INT NOT NULL, INDEX IDX_9465207D4584665A (product_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE comparison_result ADD CONSTRAINT FK_16191CA433F2B65F FOREIGN KEY (comparison_request_id) REFERENCES comparison_request (id)');
        $this->addSql('ALTER TABLE comparison_result ADD CONSTRAINT FK_16191CA492348FD2 FOREIGN KEY (tariff_id) REFERENCES tariff (id)');
        $this->addSql('ALTER TABLE insurance_product ADD CONSTRAINT FK_3E6B2108A53A8AA FOREIGN KEY (provider_id) REFERENCES insurance_provider (id)');
        $this->addSql('ALTER TABLE tariff ADD CONSTRAINT FK_9465207D4584665A FOREIGN KEY (product_id) REFERENCES insurance_product (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comparison_result DROP FOREIGN KEY FK_16191CA433F2B65F');
        $this->addSql('ALTER TABLE comparison_result DROP FOREIGN KEY FK_16191CA492348FD2');
        $this->addSql('ALTER TABLE insurance_product DROP FOREIGN KEY FK_3E6B2108A53A8AA');
        $this->addSql('ALTER TABLE tariff DROP FOREIGN KEY FK_9465207D4584665A');
        $this->addSql('DROP TABLE comparison_request');
        $this->addSql('DROP TABLE comparison_result');
        $this->addSql('DROP TABLE insurance_product');
        $this->addSql('DROP TABLE insurance_provider');
        $this->addSql('DROP TABLE tariff');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
