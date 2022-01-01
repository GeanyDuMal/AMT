<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220101200204 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE association DROP FOREIGN KEY FK_FD8521CC7597D3FE');
        $this->addSql('ALTER TABLE association ADD CONSTRAINT FK_FD8521CC7597D3FE FOREIGN KEY (member_id) REFERENCES client (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE price DROP FOREIGN KEY FK_CAC822D94584665A');
        $this->addSql('ALTER TABLE price ADD CONSTRAINT FK_CAC822D94584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');

        /*
         * if we want to use on delete NO ACTION
         * we have to add an id for the table (product-id no more a primary key)
         * */

        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13B4584665A');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13B4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE command DROP FOREIGN KEY FK_8ECAEAD419EB6921');
        $this->addSql('ALTER TABLE command ADD CONSTRAINT FK_8ECAEAD419EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON DELETE SET NULL');
        //
        $this->addSql("DROP TRIGGER IF EXISTS `db_aedi`.`modifClientTypeRemove`");
        $this->addSql(" 
        DROP TRIGGER IF EXISTS `db_aedi`.`deleteFromAssosIfChangedToStudent`;
        CREATE trigger deleteFromAssosIfChangedToStudent
                AFTER UPDATE
                on client
                for EACH ROW
            BEGIN
                DECLARE memberID integer;
                DECLARE memberType integer;
                
                select id into memberType
                from client_type
                where name='Association';
                
                select member_id into memberID
                from association a
                where a.member_id=NEW.id;
        
                if(NEW.client_type_id!=memberType)THEN
                        DELETE FROM association WHERE member_id=memberID;
                END IF;
            END");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE association DROP FOREIGN KEY FK_FD8521CC7597D3FE');
        $this->addSql('ALTER TABLE association ADD CONSTRAINT FK_FD8521CC7597D3FE FOREIGN KEY (member_id) REFERENCES client (id)');

        $this->addSql('ALTER TABLE price DROP FOREIGN KEY FK_CAC822D94584665A');
        $this->addSql('ALTER TABLE price ADD CONSTRAINT FK_CAC822D94584665A FOREIGN KEY (product_id) REFERENCES product (id)');

        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13B4584665A');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13B4584665A FOREIGN KEY (product_id) REFERENCES product (id)');

        $this->addSql('ALTER TABLE command DROP FOREIGN KEY FK_8ECAEAD419EB6921');
        $this->addSql('ALTER TABLE command ADD CONSTRAINT FK_8ECAEAD419EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
    }
}
