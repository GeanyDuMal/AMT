<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220309174127 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `purchase` DROP FOREIGN KEY `FK_6117D13B4584665A`;');
        $this->addSql('ALTER TABLE `purchase` ADD CONSTRAINT `FK_6117D13B4584665A` FOREIGN KEY (`product_id`) REFERENCES `product`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `purchase` DROP FOREIGN KEY `FK_6117D13B4584665A`;');
        $this->addSql('ALTER TABLE `purchase` ADD CONSTRAINT `FK_6117D13B4584665A` FOREIGN KEY (`product_id`) REFERENCES `product`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
');
    }
}
