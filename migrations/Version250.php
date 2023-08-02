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
                - Renommage de la table Association en Member et modification du nom de l\'attribut id
                - Création de la table Parameter
                - Suppression de la valeur par défaut du lien de l\'image d\'un post';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE association RENAME member');
        $this->addSql('ALTER TABLE member DROP FOREIGN KEY FK_FD8521CC7597D3FE');
        $this->addSql('ALTER TABLE member DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE member CHANGE member_id client_id INT NOT NULL');
        $this->addSql('ALTER TABLE member ADD CONSTRAINT FK_70E4FA7819EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE member ADD PRIMARY KEY (client_id)');

        $this->addSql('ALTER TABLE post CHANGE image_link image_link VARCHAR(255) NOT NULL');

        $this->addSql('CREATE TABLE parameter (id INT AUTO_INCREMENT NOT NULL, link_logo VARCHAR(255) NOT NULL, amount_fidelity_point_to_exchange INT NOT NULL, amount_balance_to_add_after_exchange NUMERIC(5, 2) NOT NULL, cotisant_activated TINYINT(1) NOT NULL, post_activated TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE parameter');

        $this->addSql('ALTER TABLE post CHANGE image_link image_link VARCHAR(255) DEFAULT \'https://a2mo-197c6.kxcdn.com/wp-content/uploads/2021/10/placeholder1.png\' NOT NULL');

        $this->addSql('ALTER TABLE member DROP FOREIGN KEY FK_70E4FA7819EB6921');
        $this->addSql('ALTER TABLE member DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE member CHANGE client_id member_id INT NOT NULL');
        $this->addSql('ALTER TABLE member ADD CONSTRAINT FK_FD8521CC7597D3FE FOREIGN KEY (member_id) REFERENCES client (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE member ADD PRIMARY KEY (member_id)');
        $this->addSql('ALTER TABLE member RENAME association');
    }
}
