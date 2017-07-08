<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170708075410 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_ACF41FFEA45D7E6A');
        $this->addSql('DROP INDEX IDX_ACF41FFE9B6B5FBA');
        $this->addSql('CREATE TEMPORARY TABLE __temp__balance AS SELECT account_id, purchase_order_id, amount, date FROM balance');
        $this->addSql('DROP TABLE balance');
        $this->addSql('CREATE TABLE balance (account_id INTEGER NOT NULL, purchase_order_id INTEGER NOT NULL, amount NUMERIC(10, 2) NOT NULL, date DATETIME NOT NULL, PRIMARY KEY(account_id, purchase_order_id), CONSTRAINT FK_ACF41FFE9B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_ACF41FFEA45D7E6A FOREIGN KEY (purchase_order_id) REFERENCES purchase_order (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO balance (account_id, purchase_order_id, amount, date) SELECT account_id, purchase_order_id, amount, date FROM __temp__balance');
        $this->addSql('DROP TABLE __temp__balance');
        $this->addSql('CREATE INDEX IDX_ACF41FFEA45D7E6A ON balance (purchase_order_id)');
        $this->addSql('CREATE INDEX IDX_ACF41FFE9B6B5FBA ON balance (account_id)');
        $this->addSql('DROP INDEX IDX_21E210B27E3C61F9');
        $this->addSql('DROP INDEX IDX_21E210B2D823E37A');
        $this->addSql('DROP INDEX IDX_21E210B22ADD6D8C');
        $this->addSql('DROP INDEX UNIQ_21E210B296901F54');
        $this->addSql('CREATE TEMPORARY TABLE __temp__purchase_order AS SELECT id, supplier_id, section_id, owner_id, number, type, settled, edited_at FROM purchase_order');
        $this->addSql('DROP TABLE purchase_order');
        $this->addSql('CREATE TABLE purchase_order (id INTEGER NOT NULL, supplier_id INTEGER NOT NULL, section_id INTEGER DEFAULT NULL, owner_id INTEGER DEFAULT NULL, number VARCHAR(12) NOT NULL COLLATE BINARY, type VARCHAR(10) DEFAULT NULL COLLATE BINARY, settled BOOLEAN NOT NULL, edited_at DATETIME NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_21E210B22ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_21E210B2D823E37A FOREIGN KEY (section_id) REFERENCES section (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_21E210B27E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO purchase_order (id, supplier_id, section_id, owner_id, number, type, settled, edited_at) SELECT id, supplier_id, section_id, owner_id, number, type, settled, edited_at FROM __temp__purchase_order');
        $this->addSql('DROP TABLE __temp__purchase_order');
        $this->addSql('CREATE INDEX IDX_21E210B27E3C61F9 ON purchase_order (owner_id)');
        $this->addSql('CREATE INDEX IDX_21E210B2D823E37A ON purchase_order (section_id)');
        $this->addSql('CREATE INDEX IDX_21E210B22ADD6D8C ON purchase_order (supplier_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_21E210B296901F54 ON purchase_order (number)');
        $this->addSql('DROP INDEX IDX_EC2D9ACAA45D7E6A');
        $this->addSql('DROP INDEX IDX_EC2D9ACA7E3C61F9');
        $this->addSql('CREATE TEMPORARY TABLE __temp__situation AS SELECT id, owner_id, purchase_order_id, description, created_at, edited_at FROM situation');
        $this->addSql('DROP TABLE situation');
        $this->addSql('CREATE TABLE situation (id INTEGER NOT NULL, owner_id INTEGER NOT NULL, purchase_order_id INTEGER NOT NULL, description CLOB NOT NULL COLLATE BINARY, created_at DATETIME NOT NULL, edited_at DATETIME NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_EC2D9ACA7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_EC2D9ACAA45D7E6A FOREIGN KEY (purchase_order_id) REFERENCES purchase_order (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO situation (id, owner_id, purchase_order_id, description, created_at, edited_at) SELECT id, owner_id, purchase_order_id, description, created_at, edited_at FROM __temp__situation');
        $this->addSql('DROP TABLE __temp__situation');
        $this->addSql('CREATE INDEX IDX_EC2D9ACAA45D7E6A ON situation (purchase_order_id)');
        $this->addSql('CREATE INDEX IDX_EC2D9ACA7E3C61F9 ON situation (owner_id)');
        $this->addSql('DROP INDEX UNIQ_8D93D649F85E0677');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, username, password, roles FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER NOT NULL, username VARCHAR(255) NOT NULL COLLATE BINARY, password VARCHAR(255) NOT NULL COLLATE BINARY, roles CLOB NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO user (id, username, password, roles) SELECT id, username, password, roles FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_ACF41FFE9B6B5FBA');
        $this->addSql('DROP INDEX IDX_ACF41FFEA45D7E6A');
        $this->addSql('CREATE TEMPORARY TABLE __temp__balance AS SELECT account_id, purchase_order_id, amount, date FROM balance');
        $this->addSql('DROP TABLE balance');
        $this->addSql('CREATE TABLE balance (account_id INTEGER NOT NULL, purchase_order_id INTEGER NOT NULL, amount NUMERIC(10, 2) NOT NULL, date DATETIME NOT NULL, PRIMARY KEY(account_id, purchase_order_id))');
        $this->addSql('INSERT INTO balance (account_id, purchase_order_id, amount, date) SELECT account_id, purchase_order_id, amount, date FROM __temp__balance');
        $this->addSql('DROP TABLE __temp__balance');
        $this->addSql('CREATE INDEX IDX_ACF41FFE9B6B5FBA ON balance (account_id)');
        $this->addSql('CREATE INDEX IDX_ACF41FFEA45D7E6A ON balance (purchase_order_id)');
        $this->addSql('DROP INDEX UNIQ_21E210B296901F54');
        $this->addSql('DROP INDEX IDX_21E210B22ADD6D8C');
        $this->addSql('DROP INDEX IDX_21E210B2D823E37A');
        $this->addSql('DROP INDEX IDX_21E210B27E3C61F9');
        $this->addSql('CREATE TEMPORARY TABLE __temp__purchase_order AS SELECT id, supplier_id, section_id, owner_id, number, type, settled, edited_at FROM purchase_order');
        $this->addSql('DROP TABLE purchase_order');
        $this->addSql('CREATE TABLE purchase_order (id INTEGER NOT NULL, supplier_id INTEGER NOT NULL, section_id INTEGER DEFAULT NULL, owner_id INTEGER DEFAULT NULL, number VARCHAR(12) NOT NULL, type VARCHAR(10) DEFAULT NULL, settled BOOLEAN NOT NULL, edited_at DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO purchase_order (id, supplier_id, section_id, owner_id, number, type, settled, edited_at) SELECT id, supplier_id, section_id, owner_id, number, type, settled, edited_at FROM __temp__purchase_order');
        $this->addSql('DROP TABLE __temp__purchase_order');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_21E210B296901F54 ON purchase_order (number)');
        $this->addSql('CREATE INDEX IDX_21E210B22ADD6D8C ON purchase_order (supplier_id)');
        $this->addSql('CREATE INDEX IDX_21E210B2D823E37A ON purchase_order (section_id)');
        $this->addSql('CREATE INDEX IDX_21E210B27E3C61F9 ON purchase_order (owner_id)');
        $this->addSql('DROP INDEX IDX_EC2D9ACA7E3C61F9');
        $this->addSql('DROP INDEX IDX_EC2D9ACAA45D7E6A');
        $this->addSql('CREATE TEMPORARY TABLE __temp__situation AS SELECT id, owner_id, purchase_order_id, description, created_at, edited_at FROM situation');
        $this->addSql('DROP TABLE situation');
        $this->addSql('CREATE TABLE situation (id INTEGER NOT NULL, owner_id INTEGER NOT NULL, purchase_order_id INTEGER NOT NULL, description CLOB NOT NULL, created_at DATETIME NOT NULL, edited_at DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO situation (id, owner_id, purchase_order_id, description, created_at, edited_at) SELECT id, owner_id, purchase_order_id, description, created_at, edited_at FROM __temp__situation');
        $this->addSql('DROP TABLE __temp__situation');
        $this->addSql('CREATE INDEX IDX_EC2D9ACA7E3C61F9 ON situation (owner_id)');
        $this->addSql('CREATE INDEX IDX_EC2D9ACAA45D7E6A ON situation (purchase_order_id)');
        $this->addSql('DROP INDEX UNIQ_8D93D649F85E0677');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, username, password, roles FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles CLOB NOT NULL COLLATE BINARY, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO user (id, username, password, roles) SELECT id, username, password, roles FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');
    }
}
