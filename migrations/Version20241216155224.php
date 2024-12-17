<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241216155224 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pending_registration (id INT AUTO_INCREMENT NOT NULL, verification_code VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD verification_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6491623CB0A FOREIGN KEY (verification_id) REFERENCES pending_registration (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6491623CB0A ON user (verification_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6491623CB0A');
        $this->addSql('DROP TABLE pending_registration');
        $this->addSql('DROP INDEX UNIQ_8D93D6491623CB0A ON user');
        $this->addSql('ALTER TABLE user DROP verification_id');
    }
}
