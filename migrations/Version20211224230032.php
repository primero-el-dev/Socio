<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211224230032 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER INDEX user_login_unique RENAME TO app_user_login_unique');
        $this->addSql('ALTER INDEX user_email_unique RENAME TO app_user_email_unique');
        $this->addSql('ALTER INDEX user_slug_unique RENAME TO app_user_slug_unique');
        $this->addSql('ALTER INDEX user_phone_unique RENAME TO app_user_phone_unique');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER INDEX app_user_slug_unique RENAME TO user_slug_unique');
        $this->addSql('ALTER INDEX app_user_phone_unique RENAME TO user_phone_unique');
        $this->addSql('ALTER INDEX app_user_email_unique RENAME TO user_email_unique');
        $this->addSql('ALTER INDEX app_user_login_unique RENAME TO user_login_unique');
    }
}
