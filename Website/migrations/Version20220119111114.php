<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220119111114 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post CHANGE image_link image_link VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'https://a2mo-197c6.kxcdn.com/wp-content/uploads/2021/10/placeholder1.png\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('create trigger imageOnInsert
    before insert
    on post
    for each row
begin
    IF(new.image_link is null or new.image_link=\'\') then
        set new.image_link=\'https://a2mo-197c6.kxcdn.com/wp-content/uploads/2021/10/placeholder1.png\';
    end if;
end;');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post CHANGE image_link image_link VARCHAR(255) NOT NULL');
        $this->addSql('drop trigger imageOnInsert;');
    }
}
