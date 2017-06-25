<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170625121052 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE balance (account_id INTEGER NOT NULL, purchase_order_id INTEGER NOT NULL, amount NUMERIC(10, 2) NOT NULL, date DATETIME NOT NULL, PRIMARY KEY(account_id, purchase_order_id))');
        $this->addSql('CREATE INDEX IDX_ACF41FFE9B6B5FBA ON balance (account_id)');
        $this->addSql('CREATE INDEX IDX_ACF41FFEA45D7E6A ON balance (purchase_order_id)');
        $this->addSql('CREATE TABLE purchase_order (id INTEGER NOT NULL, supplier_id INTEGER NOT NULL, section_id INTEGER DEFAULT NULL, owner_id INTEGER DEFAULT NULL, number VARCHAR(12) NOT NULL, type VARCHAR(10) DEFAULT NULL, settled BOOLEAN NOT NULL, edited_at DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_21E210B296901F54 ON purchase_order (number)');
        $this->addSql('CREATE INDEX IDX_21E210B22ADD6D8C ON purchase_order (supplier_id)');
        $this->addSql('CREATE INDEX IDX_21E210B2D823E37A ON purchase_order (section_id)');
        $this->addSql('CREATE INDEX IDX_21E210B27E3C61F9 ON purchase_order (owner_id)');
        $this->addSql('CREATE TABLE account (id INTEGER NOT NULL, number INTEGER NOT NULL, name VARCHAR(43) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7D3656A496901F54 ON account (number)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7D3656A45E237E06 ON account (name)');
        $this->addSql('CREATE TABLE situation (id INTEGER NOT NULL, owner_id INTEGER NOT NULL, purchase_order_id INTEGER NOT NULL, description CLOB NOT NULL, created_at DATETIME NOT NULL, edited_at DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EC2D9ACA7E3C61F9 ON situation (owner_id)');
        $this->addSql('CREATE INDEX IDX_EC2D9ACAA45D7E6A ON situation (purchase_order_id)');
        $this->addSql('CREATE TABLE user (id INTEGER NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles CLOB NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');
        $this->addSql('CREATE TABLE supplier (id INTEGER NOT NULL, name VARCHAR(51) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE section (id INTEGER NOT NULL, name VARCHAR(20) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2D737AEF5E237E06 ON section (name)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE balance');
        $this->addSql('DROP TABLE purchase_order');
        $this->addSql('DROP TABLE account');
        $this->addSql('DROP TABLE situation');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE supplier');
        $this->addSql('DROP TABLE section');
    }
}
