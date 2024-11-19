<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241119200949 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_suspension ADD issued_for_id INT DEFAULT NULL, ADD issued_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_suspension ADD CONSTRAINT FK_25C8C3ECA8573404 FOREIGN KEY (issued_for_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_suspension ADD CONSTRAINT FK_25C8C3EC784BB717 FOREIGN KEY (issued_by_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_25C8C3ECA8573404 ON user_suspension (issued_for_id)');
        $this->addSql('CREATE INDEX IDX_25C8C3EC784BB717 ON user_suspension (issued_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_suspension DROP FOREIGN KEY FK_25C8C3ECA8573404');
        $this->addSql('ALTER TABLE user_suspension DROP FOREIGN KEY FK_25C8C3EC784BB717');
        $this->addSql('DROP INDEX UNIQ_25C8C3ECA8573404 ON user_suspension');
        $this->addSql('DROP INDEX IDX_25C8C3EC784BB717 ON user_suspension');
        $this->addSql('ALTER TABLE user_suspension DROP issued_for_id, DROP issued_by_id');
    }
}
