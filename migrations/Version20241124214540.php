<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241124214540 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_restriction DROP FOREIGN KEY FK_D382525BA76ED395');
        $this->addSql('ALTER TABLE user_restriction DROP FOREIGN KEY FK_D382525BFED90CCA');
        $this->addSql('DROP TABLE user_restriction');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_restriction (user_id INT NOT NULL, permission_id INT NOT NULL, INDEX IDX_D382525BFED90CCA (permission_id), INDEX IDX_D382525BA76ED395 (user_id), PRIMARY KEY(user_id, permission_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE user_restriction ADD CONSTRAINT FK_D382525BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_restriction ADD CONSTRAINT FK_D382525BFED90CCA FOREIGN KEY (permission_id) REFERENCES permission (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}
