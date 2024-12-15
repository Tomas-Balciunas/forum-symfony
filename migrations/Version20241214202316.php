<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241214202316 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_settings CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_suspension DROP FOREIGN KEY FK_25C8C3EC2C085159');
        $this->addSql('ALTER TABLE user_suspension DROP FOREIGN KEY FK_25C8C3EC4E1DD2BF');
        $this->addSql('ALTER TABLE user_suspension ADD CONSTRAINT FK_25C8C3EC2C085159 FOREIGN KEY (issued_for) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_suspension ADD CONSTRAINT FK_25C8C3EC4E1DD2BF FOREIGN KEY (issued_by) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_settings DROP FOREIGN KEY FK_5C844C5A76ED395');
        $this->addSql('ALTER TABLE user_settings CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_suspension DROP FOREIGN KEY FK_25C8C3EC2C085159');
        $this->addSql('ALTER TABLE user_suspension DROP FOREIGN KEY FK_25C8C3EC4E1DD2BF');
        $this->addSql('ALTER TABLE user_suspension ADD CONSTRAINT FK_25C8C3EC2C085159 FOREIGN KEY (issued_for) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE user_suspension ADD CONSTRAINT FK_25C8C3EC4E1DD2BF FOREIGN KEY (issued_by) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
