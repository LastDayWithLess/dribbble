<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260506125314 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Создание таблицы users и связей с таблицой pivtures';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE users (
            id SERIAL NOT NULL,
            name VARCHAR(50) NOT NULL,
            email VARCHAR(254) NOT NULL,
            roles JSON NOT NULL,
            password VARCHAR(255) NOT NULL,
            PRIMARY KEY(id)
        )');
        
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON users (email)');
        
        $this->addSql('ALTER TABLE pictures ADD user_id INT NOT NULL');
        
        $this->addSql('ALTER TABLE pictures ADD CONSTRAINT FK_PICTURES_USER_ID FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        
        $this->addSql('CREATE INDEX IDX_PICTURES_USER_ID ON pictures (user_id)');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE pictures DROP CONSTRAINT FK_PICTURES_USER_ID');
        
        $this->addSql('DROP INDEX IDX_PICTURES_USER_ID');
        
        $this->addSql('ALTER TABLE pictures DROP user_id');
        
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_EMAIL');
        
        $this->addSql('DROP TABLE users');
    }
}
