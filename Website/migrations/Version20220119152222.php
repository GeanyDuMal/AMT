<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220119152222 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post CHANGE image_link image_link VARCHAR(255) DEFAULT \'https://a2mo-197c6.kxcdn.com/wp-content/uploads/2021/10/placeholder1.png\' NOT NULL');
        $this->addSql('ALTER TABLE ordered DROP FOREIGN KEY FK_F529939819EB6921');
        $this->addSql('ALTER TABLE ordered DROP FOREIGN KEY FK_F5299398DC058279');
        $this->addSql('DROP INDEX IDX_F529939819EB6921 ON ordered');
        $this->addSql('DROP INDEX IDX_F5299398DC058279 ON ordered');
        $this->addSql('CREATE INDEX IDX_C3121F9919EB6921 ON ordered (client_id)');
        $this->addSql('CREATE INDEX IDX_C3121F99DC058279 ON ordered (payment_type_id)');
        $this->addSql('ALTER TABLE ordered ADD CONSTRAINT FK_F529939819EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ordered ADD CONSTRAINT FK_F5299398DC058279 FOREIGN KEY (payment_type_id) REFERENCES payment_type (id)');
        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13B8D9F6D38');
        $this->addSql('DROP INDEX IDX_6117D13B8D9f6D38 ON purchase');
        $this->addSql('CREATE INDEX IDX_6117D13BAA60395A ON purchase (ordered_id)');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13B8D9F6D38 FOREIGN KEY (ordered_id) REFERENCES ordered (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post CHANGE image_link image_link VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'https://a2mo-197c6.kxcdn.com/wp-content/uploads/2021/10/placeholder1.png\' COLLATE `utf8mb4_unicode_ci`');

        //Ce que moi je fait
        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13B8D9F6D38 ');
        $this->addSql('DROP INDEX IDX_6117D13BAA60395A ON purchase');
        $this->addSql('CREATE INDEX IDX_6117D13B8D9f6D38 ON purchase');


        //Ce qui a été généré de base
        $this->addSql('ALTER TABLE ordered DROP FOREIGN KEY FK_C3121F9919EB6921');
        $this->addSql('ALTER TABLE ordered DROP FOREIGN KEY FK_C3121F9919EB6921');
        $this->addSql('ALTER TABLE ordered DROP FOREIGN KEY FK_C3121F99DC058279');
        $this->addSql('ALTER TABLE ordered ADD CONSTRAINT FK_F529939819EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON DELETE SET NULL');
        $this->addSql('DROP INDEX idx_c3121f9919eb6921 ON ordered');
        $this->addSql('CREATE INDEX IDX_F529939819EB6921 ON ordered (client_id)');
        $this->addSql('DROP INDEX idx_c3121f99dc058279 ON ordered');
        $this->addSql('CREATE INDEX IDX_F5299398DC058279 ON ordered (payment_type_id)');
        $this->addSql('ALTER TABLE ordered ADD CONSTRAINT FK_C3121F9919EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE ordered ADD CONSTRAINT FK_C3121F99DC058279 FOREIGN KEY (payment_type_id) REFERENCES payment_type (id)');
        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13BAA60395A');
        $this->addSql('DROP INDEX idx_6117d13baa60395a ON purchase');
        $this->addSql('CREATE INDEX IDX_6117D13B8D9F6D38 ON purchase (ordered_id)');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13BAA60395A FOREIGN KEY (ordered_id) REFERENCES ordered (id)');
    }
}
