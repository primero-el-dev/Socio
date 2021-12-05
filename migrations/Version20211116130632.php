<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211116130632 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE user_user_relation_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE user_subject_relation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE user_subject_relation (id INT NOT NULL, who_id INT DEFAULT NULL, action VARCHAR(255) NOT NULL, denied BOOLEAN DEFAULT \'false\' NOT NULL, terminated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, subject_type VARCHAR(255) NOT NULL, subject_iri VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_27E145FCF4E25B21 ON user_subject_relation (who_id)');
        $this->addSql('COMMENT ON COLUMN user_subject_relation.terminated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE user_subject_relation ADD CONSTRAINT FK_27E145FCF4E25B21 FOREIGN KEY (who_id) REFERENCES app_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE user_user_relation');
        $this->addSql('ALTER TABLE reaction ADD comment_id INT NOT NULL');
        $this->addSql('ALTER TABLE reaction ADD CONSTRAINT FK_A4D707F7F8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_A4D707F7F8697D13 ON reaction (comment_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE user_subject_relation_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE user_user_relation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE user_user_relation (id INT NOT NULL, who_id INT DEFAULT NULL, whom_id INT DEFAULT NULL, action VARCHAR(255) NOT NULL, denied BOOLEAN DEFAULT \'false\' NOT NULL, terminated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_6c8a0e191b2a6f8c ON user_user_relation (whom_id)');
        $this->addSql('CREATE INDEX idx_6c8a0e19f4e25b21 ON user_user_relation (who_id)');
        $this->addSql('COMMENT ON COLUMN user_user_relation.terminated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE user_user_relation ADD CONSTRAINT fk_6c8a0e19f4e25b21 FOREIGN KEY (who_id) REFERENCES app_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_user_relation ADD CONSTRAINT fk_6c8a0e191b2a6f8c FOREIGN KEY (whom_id) REFERENCES app_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE user_subject_relation');
        $this->addSql('ALTER TABLE reaction DROP CONSTRAINT FK_A4D707F7F8697D13');
        $this->addSql('DROP INDEX IDX_A4D707F7F8697D13');
        $this->addSql('ALTER TABLE reaction DROP comment_id');
    }
}
