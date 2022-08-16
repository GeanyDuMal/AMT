<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220726104711 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration supprimant les autorisations de null à certains champs';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client CHANGE balance balance NUMERIC(5, 2) DEFAULT \'0.00\' NOT NULL, CHANGE fidelity_point fidelity_point INT DEFAULT 0 NOT NULL');

        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13B8D9F6D38');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13BAA60395A FOREIGN KEY (ordered_id) REFERENCES ordered (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client CHANGE balance balance NUMERIC(5, 2) DEFAULT \'0.00\', CHANGE fidelity_point fidelity_point INT DEFAULT 0');

        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13BAA60395A');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13B8D9F6D38 FOREIGN KEY (ordered_id) REFERENCES ordered (id)');
    }
}
