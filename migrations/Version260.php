<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version260 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration concernant le version 2.6.0 : 
                - Ajout d\'un utilisateur admin par défaut';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('INSERT INTO Client (name, first_name, login, password, balance, fidelity_point, client_type, roles, creation_date) VALUES (\'Admin\', \'Admin\', \'Admin.\', \'$2y$13$PPbBuWzBd0eeoUhTSGRZm.AAdoY4uEMvvocU4Z9fXAu7LqFyqxMVO\', \'0.00\', 0, \'Administrateur\', \'["ROLE_ADMIN"]\', NOW())');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM Client WHERE login = \'Admin.\' AND name = \'Admin\' AND first_name = \'Admin\'');
    }
}
