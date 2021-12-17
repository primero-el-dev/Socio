<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211216150226 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE UNIQUE INDEX phone_unique ON app_user (phone) WHERE (deleted_at IS NULL)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX phone_unique');
    }
}
