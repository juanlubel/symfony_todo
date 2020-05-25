<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200525092915 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE board (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(80) NOT NULL, category VARCHAR(50) NOT NULL)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__task AS SELECT id, title, description FROM task');
        $this->addSql('DROP TABLE task');
        $this->addSql('CREATE TABLE task (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, board_id INTEGER DEFAULT NULL, title VARCHAR(80) NOT NULL COLLATE BINARY, description VARCHAR(255) NOT NULL COLLATE BINARY, CONSTRAINT FK_527EDB25E7EC5785 FOREIGN KEY (board_id) REFERENCES board (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO task (id, title, description) SELECT id, title, description FROM __temp__task');
        $this->addSql('DROP TABLE __temp__task');
        $this->addSql('CREATE INDEX IDX_527EDB25E7EC5785 ON task (board_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE board');
        $this->addSql('DROP INDEX IDX_527EDB25E7EC5785');
        $this->addSql('CREATE TEMPORARY TABLE __temp__task AS SELECT id, title, description FROM task');
        $this->addSql('DROP TABLE task');
        $this->addSql('CREATE TABLE task (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(80) NOT NULL, description VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO task (id, title, description) SELECT id, title, description FROM __temp__task');
        $this->addSql('DROP TABLE __temp__task');
    }
}
