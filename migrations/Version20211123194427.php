<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211123194427 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE dictionary_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE notification_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE dictionary (id INT NOT NULL, value VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE notification (id INT NOT NULL, author_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, message TEXT NOT NULL, subject_iri VARCHAR(255) NOT NULL, approved BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BF5476CAF675F31B ON notification (author_id)');
        $this->addSql('COMMENT ON COLUMN notification.created_at IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAF675F31B FOREIGN KEY (author_id) REFERENCES app_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql("INSERT INTO dictionary (id, type, value) VALUES (NEXTVAL('dictionary_id_seq'), 'REACTION', 'LIKE')");
        $this->addSql("INSERT INTO dictionary (id, type, value) VALUES (NEXTVAL('dictionary_id_seq'), 'REACTION', 'DISLIKE')");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM dictionary WHERE type='REACTION'");
        $this->addSql('DROP SEQUENCE dictionary_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE notification_id_seq CASCADE');
        $this->addSql('DROP TABLE dictionary');
        $this->addSql('DROP TABLE notification');
    }
}
