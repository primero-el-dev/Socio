<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211123231916 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
CREATE OR REPLACE FUNCTION JSON_CONTAINS(json, text) 
RETURNS boolean as $$
BEGIN
    RETURN (CAST($1 AS TEXT) LIKE '%' || $2 || '%');
END; 
$$ LANGUAGE plpgsql;
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP FUNCTION IF EXISTS JSON_CONTAINS(json, text)');
    }
}
