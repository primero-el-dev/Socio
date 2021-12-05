<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211124130457 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE notification DROP CONSTRAINT fk_bf5476caf675f31b');
        $this->addSql('DROP INDEX idx_bf5476caf675f31b');
        $this->addSql('ALTER TABLE notification ADD seen BOOLEAN DEFAULT \'false\' NOT NULL');
        $this->addSql('ALTER TABLE notification ADD deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE notification DROP approved');
        $this->addSql('ALTER TABLE notification DROP updated_at');
        $this->addSql('ALTER TABLE notification RENAME COLUMN author_id TO recipient_id');
        $this->addSql('COMMENT ON COLUMN notification.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAE92F8F78 FOREIGN KEY (recipient_id) REFERENCES app_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_BF5476CAE92F8F78 ON notification (recipient_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE notification DROP CONSTRAINT FK_BF5476CAE92F8F78');
        $this->addSql('DROP INDEX IDX_BF5476CAE92F8F78');
        $this->addSql('ALTER TABLE notification ADD approved BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE notification ADD updated_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE notification DROP seen');
        $this->addSql('ALTER TABLE notification DROP deleted_at');
        $this->addSql('ALTER TABLE notification RENAME COLUMN recipient_id TO author_id');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT fk_bf5476caf675f31b FOREIGN KEY (author_id) REFERENCES app_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_bf5476caf675f31b ON notification (author_id)');
    }
}
