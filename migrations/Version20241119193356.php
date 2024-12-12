<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241119193356 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE role DROP FOREIGN KEY FK_57698A6AD60322AC');
        $this->addSql('DROP INDEX IDX_57698A6AD60322AC ON role');
        $this->addSql('ALTER TABLE role CHANGE role_id permissions_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE role ADD CONSTRAINT FK_57698A6A9C3E4F87 FOREIGN KEY (permissions_id) REFERENCES permission (id)');
        $this->addSql('CREATE INDEX IDX_57698A6A9C3E4F87 ON role (permissions_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE role DROP FOREIGN KEY FK_57698A6A9C3E4F87');
        $this->addSql('DROP INDEX IDX_57698A6A9C3E4F87 ON role');
        $this->addSql('ALTER TABLE role CHANGE permissions_id role_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE role ADD CONSTRAINT FK_57698A6AD60322AC FOREIGN KEY (role_id) REFERENCES permission (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_57698A6AD60322AC ON role (role_id)');
    }
}
