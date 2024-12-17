<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241216155729 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pending_registration ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pending_registration ADD CONSTRAINT FK_FEFA0581A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FEFA0581A76ED395 ON pending_registration (user_id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6491623CB0A');
        $this->addSql('DROP INDEX UNIQ_8D93D6491623CB0A ON user');
        $this->addSql('ALTER TABLE user DROP verification_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pending_registration DROP FOREIGN KEY FK_FEFA0581A76ED395');
        $this->addSql('DROP INDEX UNIQ_FEFA0581A76ED395 ON pending_registration');
        $this->addSql('ALTER TABLE pending_registration DROP user_id');
        $this->addSql('ALTER TABLE user ADD verification_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6491623CB0A FOREIGN KEY (verification_id) REFERENCES pending_registration (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6491623CB0A ON user (verification_id)');
    }
}
