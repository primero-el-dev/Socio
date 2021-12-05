<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211115110710 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE reaction_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE reaction (id INT NOT NULL, author_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A4D707F7F675F31B ON reaction (author_id)');
        $this->addSql('ALTER TABLE reaction ADD CONSTRAINT FK_A4D707F7F675F31B FOREIGN KEY (author_id) REFERENCES app_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_user_relation ADD action VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user_user_relation ADD denied BOOLEAN DEFAULT \'false\' NOT NULL');
        $this->addSql('ALTER TABLE user_user_relation ADD terminated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN user_user_relation.terminated_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE reaction_id_seq CASCADE');
        $this->addSql('DROP TABLE reaction');
        $this->addSql('ALTER TABLE user_user_relation DROP action');
        $this->addSql('ALTER TABLE user_user_relation DROP denied');
        $this->addSql('ALTER TABLE user_user_relation DROP terminated_at');
    }
}
