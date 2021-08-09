<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210807153449 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE [user] (id INT IDENTITY NOT NULL, nick NVARCHAR(255) NOT NULL, email NVARCHAR(255) NOT NULL, password NVARCHAR(255) NOT NULL, roles VARCHAR(MAX) NOT NULL, last_login_date DATETIME2(6), create_account_date DATETIME2(6) NOT NULL, accept_terms_date DATETIME2(6) NOT NULL, is_active BIT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:json)\', N\'SCHEMA\', \'dbo\', N\'TABLE\', \'user\', N\'COLUMN\', roles');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA db_accessadmin');
        $this->addSql('CREATE SCHEMA db_backupoperator');
        $this->addSql('CREATE SCHEMA db_datareader');
        $this->addSql('CREATE SCHEMA db_datawriter');
        $this->addSql('CREATE SCHEMA db_ddladmin');
        $this->addSql('CREATE SCHEMA db_denydatareader');
        $this->addSql('CREATE SCHEMA db_denydatawriter');
        $this->addSql('CREATE SCHEMA db_owner');
        $this->addSql('CREATE SCHEMA db_securityadmin');
        $this->addSql('CREATE SCHEMA dbo');
        $this->addSql('DROP TABLE [user]');
        $this->addSql('
                        IF EXISTS (SELECT * FROM sysobjects WHERE name = \'menu_primary_key_uuid\')
                            ALTER TABLE menu DROP CONSTRAINT menu_primary_key_uuid
                        ELSE
                            DROP INDEX menu_primary_key_uuid ON menu
                    ');
        $this->addSql('ALTER TABLE menu ADD PRIMARY KEY (id)');
    }
}

// exception solution: https://stackoverflow.com/questions/53401131/error-when-making-a-migration-on-doctrine
