<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version250 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration concernant le version 2.5.0 : 
                - Renommage de la table Association en Member et mdofication du nom de l\'attribut id';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE association RENAME member');
        $this->addSql('ALTER TABLE member DROP FOREIGN KEY FK_FD8521CC7597D3FE');
        $this->addSql('ALTER TABLE member DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE member CHANGE member_id client_id INT NOT NULL');
        $this->addSql('ALTER TABLE member ADD CONSTRAINT FK_70E4FA7819EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE member ADD PRIMARY KEY (client_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE member DROP FOREIGN KEY FK_70E4FA7819EB6921');
        $this->addSql('ALTER TABLE member DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE member CHANGE client_id member_id INT NOT NULL');
        $this->addSql('ALTER TABLE member ADD CONSTRAINT FK_FD8521CC7597D3FE FOREIGN KEY (member_id) REFERENCES client (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE member ADD PRIMARY KEY (member_id)');
        $this->addSql('ALTER TABLE member RENAME association');
    }
}
