<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210907163523 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE guild_members DROP CONSTRAINT FK_guild_member_guild');
        $this->addSql('DROP TABLE guild');
        $this->addSql('DROP TABLE guild_members');
        $this->addSql('DROP TABLE session');
        $this->addSql('ALTER TABLE [user] ALTER COLUMN id UNIQUEIDENTIFIER NOT NULL');
        $this->addSql('ALTER TABLE [user] DROP CONSTRAINT DF_8D93D649_B63E2EC7');
        $this->addSql('ALTER TABLE [user] ALTER COLUMN roles VARCHAR(MAX) NOT NULL');
        $this->addSql('ALTER TABLE [user] ADD CONSTRAINT DF_8D93D649_B63E2EC7 DEFAULT \'[\'\'ROLE_USER\'\']\' FOR roles');
        $this->addSql('ALTER TABLE [user] DROP CONSTRAINT DF_8D93D649_1B5771DD');
        $this->addSql('ALTER TABLE [user] ALTER COLUMN is_active BIT NOT NULL');
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:json)\', N\'SCHEMA\', \'dbo\', N\'TABLE\', \'user\', N\'COLUMN\', roles');
        $this->addSql('CREATE INDEX FK_session_user ON [user] (id)');
        $this->addSql('CREATE UNIQUE INDEX IX_user_create_account_date ON [user] (create_account_date) WHERE create_account_date IS NOT NULL');
        $this->addSql('ALTER TABLE avatar ALTER COLUMN id UNIQUEIDENTIFIER NOT NULL');
        $this->addSql('ALTER TABLE avatar ALTER COLUMN user_id UNIQUEIDENTIFIER');
        $this->addSql('ALTER TABLE avatar ADD CONSTRAINT DF_1677722F_A76ED395 DEFAULT \'newid()\' FOR user_id');
        $this->addSql('ALTER TABLE avatar DROP CONSTRAINT DF_1677722F_E818BD5');
        $this->addSql('ALTER TABLE avatar ALTER COLUMN coins INT NOT NULL');
        $this->addSql('CREATE INDEX IX_avatar_nick ON avatar (nick)');
        $this->addSql('CREATE UNIQUE INDEX UNQ_IX_avatar_nickname ON avatar (nickname) WHERE nickname IS NOT NULL');
        $this->addSql('ALTER TABLE menu DROP CONSTRAINT DF_7D053A93_BF396750');
        $this->addSql('ALTER TABLE menu ALTER COLUMN id NVARCHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE menu ALTER COLUMN parent_id NVARCHAR(36)');
        $this->addSql('EXEC sp_rename N\'menu.fk_menu_parent_uuid\', N\'menu_foreign_key_uuid\', N\'INDEX\'');
        $this->addSql('ALTER TABLE user_keys ALTER COLUMN id UNIQUEIDENTIFIER NOT NULL');
        $this->addSql('ALTER TABLE user_keys ALTER COLUMN user_id UNIQUEIDENTIFIER');
        $this->addSql('ALTER TABLE user_keys ADD CONSTRAINT DF_CE0F0825_A76ED395 DEFAULT \'newid()\' FOR user_id');
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
        $this->addSql('CREATE SCHEMA webpage');
        $this->addSql('CREATE TABLE guild (id UNIQUEIDENTIFIER NOT NULL, name NVARCHAR(35) COLLATE Polish_CI_AS NOT NULL, level SMALLINT NOT NULL, coins INT NOT NULL, type NVARCHAR(35) COLLATE Polish_CI_AS NOT NULL, create_date DATETIME2(6) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE NONCLUSTERED INDEX UNQ_guild ON guild (name, type) WHERE name IS NOT NULL AND type IS NOT NULL');
        $this->addSql('ALTER TABLE guild ADD CONSTRAINT DF_75407DAB_BF396750 DEFAULT \'newid()\' FOR id');
        $this->addSql('ALTER TABLE guild ADD CONSTRAINT DF_75407DAB_9AEACC13 DEFAULT 0 FOR level');
        $this->addSql('ALTER TABLE guild ADD CONSTRAINT DF_75407DAB_E818BD5 DEFAULT 0 FOR coins');
        $this->addSql('ALTER TABLE guild ADD CONSTRAINT DF_75407DAB_2B3FAA13 DEFAULT CURRENT_TIMESTAMP FOR create_date');
        $this->addSql('CREATE TABLE guild_members (guild_id UNIQUEIDENTIFIER NOT NULL, avatar_id UNIQUEIDENTIFIER NOT NULL, join_date DATETIME2(6) NOT NULL, role NVARCHAR(50) COLLATE Polish_CI_AS NOT NULL, PRIMARY KEY (guild_id, avatar_id))');
        $this->addSql('CREATE INDEX IDX_751A1C605F2131EF ON guild_members (guild_id)');
        $this->addSql('CREATE INDEX IDX_751A1C6086383B10 ON guild_members (avatar_id)');
        $this->addSql('ALTER TABLE guild_members ADD CONSTRAINT DF_751A1C60_3BC67AE3 DEFAULT CURRENT_TIMESTAMP FOR join_date');
        $this->addSql('ALTER TABLE guild_members ADD CONSTRAINT DF_751A1C60_57698A6A DEFAULT \'["ROLE_MEMBER"]\' FOR role');
        $this->addSql('CREATE TABLE session (id UNIQUEIDENTIFIER NOT NULL, user_id UNIQUEIDENTIFIER, session_key NVARCHAR(255) COLLATE Polish_CI_AS NOT NULL, guest_ip NVARCHAR(255) COLLATE Polish_CI_AS NOT NULL, browser_data NVARCHAR(255) COLLATE Polish_CI_AS, create_time DATETIME2(6) NOT NULL, expiration_date DATETIME2(6) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE NONCLUSTERED INDEX FK_session_user ON session (user_id)');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT DF_D044D5D4_BF396750 DEFAULT \'newid()\' FOR id');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT DF_D044D5D4_EE35052C DEFAULT CURRENT_TIMESTAMP FOR create_time');
        $this->addSql('ALTER TABLE guild_members ADD CONSTRAINT FK_guild_member_guild FOREIGN KEY (guild_id) REFERENCES guild (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE guild_members ADD CONSTRAINT FK_guild_member_avatar FOREIGN KEY (avatar_id) REFERENCES avatar (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_session_user FOREIGN KEY (user_id) REFERENCES [user] (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('
                        IF EXISTS (SELECT * FROM sysobjects WHERE name = \'IX_avatar_nick\')
                            ALTER TABLE avatar DROP CONSTRAINT IX_avatar_nick
                        ELSE
                            DROP INDEX IX_avatar_nick ON avatar
                    ');
        $this->addSql('
                        IF EXISTS (SELECT * FROM sysobjects WHERE name = \'UNQ_IX_avatar_nickname\')
                            ALTER TABLE avatar DROP CONSTRAINT UNQ_IX_avatar_nickname
                        ELSE
                            DROP INDEX UNQ_IX_avatar_nickname ON avatar
                    ');
        $this->addSql('ALTER TABLE avatar ALTER COLUMN id UNIQUEIDENTIFIER NOT NULL');
        $this->addSql('ALTER TABLE avatar DROP CONSTRAINT DF_1677722F_A76ED395');
        $this->addSql('ALTER TABLE avatar ALTER COLUMN user_id UNIQUEIDENTIFIER NOT NULL');
        $this->addSql('ALTER TABLE avatar ALTER COLUMN coins INT NOT NULL');
        $this->addSql('ALTER TABLE avatar ADD CONSTRAINT DF_1677722F_E818BD5 DEFAULT 0 FOR coins');
        $this->addSql('ALTER TABLE menu ALTER COLUMN id UNIQUEIDENTIFIER NOT NULL');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT DF_7D053A93_BF396750 DEFAULT \'newid()\' FOR id');
        $this->addSql('ALTER TABLE menu ALTER COLUMN parent_id UNIQUEIDENTIFIER');
        $this->addSql('EXEC sp_rename N\'menu.menu_foreign_key_uuid\', N\'FK_menu_parent_uuid\', N\'INDEX\'');
        $this->addSql('
                        IF EXISTS (SELECT * FROM sysobjects WHERE name = \'FK_session_user\')
                            ALTER TABLE [user] DROP CONSTRAINT FK_session_user
                        ELSE
                            DROP INDEX FK_session_user ON [user]
                    ');
        $this->addSql('
                        IF EXISTS (SELECT * FROM sysobjects WHERE name = \'IX_user_create_account_date\')
                            ALTER TABLE [user] DROP CONSTRAINT IX_user_create_account_date
                        ELSE
                            DROP INDEX IX_user_create_account_date ON [user]
                    ');
        $this->addSql('ALTER TABLE [user] ALTER COLUMN id UNIQUEIDENTIFIER NOT NULL');
        $this->addSql('ALTER TABLE [user] DROP CONSTRAINT DF_8D93D649_B63E2EC7');
        $this->addSql('ALTER TABLE [user] ALTER COLUMN roles NVARCHAR(255) COLLATE Polish_CI_AS NOT NULL');
        $this->addSql('ALTER TABLE [user] ADD CONSTRAINT DF_8D93D649_B63E2EC7 DEFAULT \'["ROLE_USER"]\' FOR roles');
        $this->addSql('ALTER TABLE [user] ALTER COLUMN is_active BIT NOT NULL');
        $this->addSql('ALTER TABLE [user] ADD CONSTRAINT DF_8D93D649_1B5771DD DEFAULT \'0\' FOR is_active');
        $this->addSql('EXEC sp_dropextendedproperty N\'MS_Description\', N\'SCHEMA\', \'dbo\', N\'TABLE\', \'user\', N\'COLUMN\', roles');
        $this->addSql('ALTER TABLE user_keys ALTER COLUMN id UNIQUEIDENTIFIER NOT NULL');
        $this->addSql('ALTER TABLE user_keys DROP CONSTRAINT DF_CE0F0825_A76ED395');
        $this->addSql('ALTER TABLE user_keys ALTER COLUMN user_id UNIQUEIDENTIFIER NOT NULL');
    }
}
