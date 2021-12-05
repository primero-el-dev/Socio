<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211115123455 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT fk_9474526c4b89032c');
        $this->addSql('DROP INDEX idx_9474526c4b89032c');
        $this->addSql('ALTER TABLE comment ADD thread_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comment ADD parent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comment RENAME COLUMN post_id TO group_id');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CFE54D947 FOREIGN KEY (group_id) REFERENCES "group" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CE2904019 FOREIGN KEY (thread_id) REFERENCES thread (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C727ACA70 FOREIGN KEY (parent_id) REFERENCES comment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_9474526CFE54D947 ON comment (group_id)');
        $this->addSql('CREATE INDEX IDX_9474526CE2904019 ON comment (thread_id)');
        $this->addSql('CREATE INDEX IDX_9474526C727ACA70 ON comment (parent_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526CFE54D947');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526CE2904019');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526C727ACA70');
        $this->addSql('DROP INDEX IDX_9474526CFE54D947');
        $this->addSql('DROP INDEX IDX_9474526CE2904019');
        $this->addSql('DROP INDEX IDX_9474526C727ACA70');
        $this->addSql('ALTER TABLE comment ADD post_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comment DROP group_id');
        $this->addSql('ALTER TABLE comment DROP thread_id');
        $this->addSql('ALTER TABLE comment DROP parent_id');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT fk_9474526c4b89032c FOREIGN KEY (post_id) REFERENCES post (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_9474526c4b89032c ON comment (post_id)');
    }
}
