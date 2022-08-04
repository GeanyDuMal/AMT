<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration pour la version 2.0.0';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE association (member_id INT NOT NULL, role VARCHAR(255) NOT NULL, PRIMARY KEY(member_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, login VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, balance NUMERIC(5, 2) DEFAULT \'0.00\' NOT NULL, fidelity_point INT DEFAULT 0 NOT NULL, client_type VARCHAR(255) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', UNIQUE INDEX UNIQ_C7440455AA08CB10 (login), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ordered (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, ordered_at DATETIME NOT NULL, payment_type VARCHAR(255) NOT NULL, INDEX IDX_C3121F9919EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, image_link VARCHAR(255) DEFAULT \'https://a2mo-197c6.kxcdn.com/wp-content/uploads/2021/10/placeholder1.png\' NOT NULL, post_type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE price (client_type VARCHAR(255) NOT NULL, product_id INT NOT NULL, price NUMERIC(5, 2) NOT NULL, INDEX IDX_CAC822D94584665A (product_id), PRIMARY KEY(product_id, client_type)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, product_type VARCHAR(255) NOT NULL, quantity_stock INT NOT NULL, image_link VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE purchase (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, ordered_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_6117D13B4584665A (product_id), INDEX IDX_6117D13BAA60395A (ordered_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE association ADD CONSTRAINT FK_FD8521CC7597D3FE FOREIGN KEY (member_id) REFERENCES client (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ordered ADD CONSTRAINT FK_C3121F9919EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE price ADD CONSTRAINT FK_CAC822D94584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13B4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13BAA60395A FOREIGN KEY (ordered_id) REFERENCES ordered (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE association DROP FOREIGN KEY FK_FD8521CC7597D3FE');
        $this->addSql('ALTER TABLE ordered DROP FOREIGN KEY FK_C3121F9919EB6921');
        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13BAA60395A');
        $this->addSql('ALTER TABLE price DROP FOREIGN KEY FK_CAC822D94584665A');
        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13B4584665A');
        $this->addSql('DROP TABLE IF EXISTS association');
        $this->addSql('DROP TABLE IF EXISTS client');
        $this->addSql('DROP TABLE IF EXISTS ordered');
        $this->addSql('DROP TABLE IF EXISTS post');
        $this->addSql('DROP TABLE IF EXISTS price');
        $this->addSql('DROP TABLE IF EXISTS product');
        $this->addSql('DROP TABLE IF EXISTS purchase');
    }
}
