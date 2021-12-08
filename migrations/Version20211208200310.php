<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211208200310 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP INDEX email_unique');
        $this->addSql('ALTER TABLE app_user ADD slug VARCHAR(255)');
        $this->addSql("UPDATE app_user SET slug = 'primero'");
        $this->addSql('ALTER TABLE app_user ALTER COLUMN slug SET NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX email_unique ON app_user (slug) WHERE (deleted_at IS NULL)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX email_unique');
        $this->addSql('ALTER TABLE app_user DROP slug');
        $this->addSql('CREATE UNIQUE INDEX email_unique ON app_user (email, deleted_at)');
    }
}
