<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211109143914 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO app_user (id, email, password, roles, created_at, birth) VALUES (NEXTVAL('app_user_id_seq'), 'primero.el.dev@gmail.com', '\$2a\$12\$StEz7xUH3VlWFNr1LuXZwuLYsmgemBBTaZQ0hV9EXCM1u..poMguK', '[\"ROLE_USER\"]', NOW(), '1994-01-01 00:00:00')");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM app_user WHERE email='primero.el.dev@gmail.com'");
    }
}
