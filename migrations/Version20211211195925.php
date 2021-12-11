<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211211195925 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX email_unique');
        $this->addSql('ALTER TABLE app_user ADD login VARCHAR(255)');
        $this->addSql("UPDATE app_user SET login = 'login' WHERE id = 2");
        $this->addSql('ALTER TABLE app_user ALTER COLUMN login SET NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX login_unique ON app_user (login) WHERE (deleted_at IS NULL)');
        $this->addSql('CREATE UNIQUE INDEX email_unique ON app_user (email) WHERE (deleted_at IS NULL)');
        $this->addSql('ALTER TABLE notification ADD message_subject VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX login_unique');
        $this->addSql('DROP INDEX slug_unique');
        $this->addSql('DROP INDEX email_unique');
        $this->addSql('ALTER TABLE app_user DROP login');
        $this->addSql('CREATE UNIQUE INDEX email_unique ON app_user (slug) WHERE (deleted_at IS NULL)');
        $this->addSql('ALTER TABLE notification DROP message_subject');
    }
}
