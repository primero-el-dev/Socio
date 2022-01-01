<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211224225702 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX user_slug_unique ON app_user (slug) WHERE (deleted_at IS NULL)');
        $this->addSql('ALTER INDEX login_unique RENAME TO user_login_unique');
        $this->addSql('ALTER INDEX email_unique RENAME TO user_email_unique');
        $this->addSql('ALTER INDEX phone_unique RENAME TO user_phone_unique');
        $this->addSql('ALTER INDEX slug_unique RENAME TO group_slug_unique');
        $this->addSql('ALTER TABLE media_object ADD owner_iri VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE media_object ADD type VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX user_slug_unique');
        $this->addSql('ALTER INDEX user_phone_unique RENAME TO phone_unique');
        $this->addSql('ALTER INDEX user_email_unique RENAME TO email_unique');
        $this->addSql('ALTER INDEX user_login_unique RENAME TO login_unique');
        $this->addSql('ALTER INDEX group_slug_unique RENAME TO slug_unique');
        $this->addSql('ALTER TABLE media_object DROP owner_iri');
        $this->addSql('ALTER TABLE media_object DROP type');
    }
}
