<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220131174928 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("DROP TRIGGER IF EXISTS modifClientTypeAdd");
        $this->addSql("DROP TRIGGER IF EXISTS deleteFromAssosIfChangedToStudent");
        $this->addSql("DROP TRIGGER IF EXISTS verifCreationClient");
        $this->addSql("DROP TRIGGER IF EXISTS verifSoldeAndFidelity");
        $this->addSql("DROP TRIGGER IF EXISTS verifClientNonNull");
        $this->addSql("DROP TRIGGER IF EXISTS imageOnInsert");
        $this->addSql("DROP TRIGGER IF EXISTS imageOnUpdate");
        $this->addSql("DROP TRIGGER IF EXISTS imageProductOnInsert");
        $this->addSql("DROP TRIGGER IF EXISTS imageProductOnUpdate");
        $this->addSql("DROP TRIGGER IF EXISTS removeQteProductFromPurchase");
        $this->addSql("DROP TRIGGER IF EXISTS verifDispoProduit");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TRIGGER modifClientTypeAdd 
                            AFTER INSERT ON association FOR EACH ROW
                            UPDATE client SET client_type_id = (SELECT id 
                                                                FROM client_type
									                            WHERE name = "Association")');
        $this->addSql("CREATE TRIGGER deleteFromAssosIfChangedToStudent
                                AFTER UPDATE ON client
                                FOR EACH ROW
                                BEGIN
                                    DECLARE memberID INTEGER;
                                    DECLARE memberType INTEGER;
                                
                                    SELECT id INTO memberType
                                    FROM client_type
                                    WHERE name='Association';
                                
                                    SELECT member_id into memberID
                                    FROM association a
                                    WHERE a.member_id=NEW.id;
                        
                                    IF(NEW.client_type_id!=memberType)THEN
                                        DELETE FROM association WHERE member_id=memberID;
                                    END IF;
                                END");
        $this->addSql('CREATE TRIGGER verifCreationClient BEFORE INSERT ON client FOR EACH ROW
                            BEGIN
                                IF(NEW.balance < 0 || ISNULL(NEW.balance)) THEN
                                    SET NEW.balance = 0;
                                END IF;
                                
                                IF(NEW.fidelity_point <> 0 || ISNULL(NEW.fidelity_point)) THEN
                                    SET NEW.fidelity_point = 0;
                                END IF;
                            END;');
        $this->addSql('DROP TRIGGER IF EXISTS verifSoldePositif;
                            CREATE TRIGGER verifSoldeAndFidelity 
                                BEFORE UPDATE ON client FOR EACH ROW
                                BEGIN
                                    IF(NEW.balance < 0) THEN
                                        SIGNAL SQLSTATE "45000"
                                        SET MESSAGE_TEXT = "Solde negatif, erreur Update";
                                    END IF;
                                    IF(NEW.fidelity_point >= 150)THEN
                                        BEGIN
                                            SET NEW.fidelity_point = NEW.fidelity_point - 150;
                                            SET NEW.balance = NEW.balance + 0.8; 
                                        END;
                                    END IF;
                                END;');
        $this->addSql('CREATE TRIGGER verifClientNonNull 
                            BEFORE INSERT ON `ordered` FOR EACH ROW
                            IF (ISNULL(NEW.client_id) && (SELECT name FROM payment_type WHERE id = NEW.payment_type_id) = "Solde") THEN
                                    SIGNAL SQLSTATE "45000"
                                    SET MESSAGE_TEXT = "Type de paiement incorrect, solde + client inconnu, erreur creation commande";
                            END IF;');
        $this->addSql('CREATE TRIGGER imageOnInsert
                            BEFORE INSERT
                            ON post
                            FOR EACH ROW
                            BEGIN
                                IF(new.image_link is null or new.image_link=\'\') THEN
                                    SET new.image_link=\'https://a2mo-197c6.kxcdn.com/wp-content/uploads/2021/10/placeholder1.png\';
                                END IF;
                            END;');

        $this->addSql('CREATE TRIGGER imageOnUpdate
                            BEFORE UPDATE
                            ON post
                            FOR EACH ROW
                            BEGIN
                                IF(new.image_link is null or new.image_link=\'\') THEN
                                    SET new.image_link=\'https://a2mo-197c6.kxcdn.com/wp-content/uploads/2021/10/placeholder1.png\';
                                END IF;
                            END;');
        $this->addSql('CREATE TRIGGER imageProductOnInsert
                            BEFORE INSERT
                            ON product
                            FOR EACH ROW
                            BEGIN
                                IF(new.image_link is null or new.image_link=\'\') THEN
                                    SET new.image_link=\'https://a2mo-197c6.kxcdn.com/wp-content/uploads/2021/10/placeholder1.png\';
                                END IF;
                            END;');

        $this->addSql('CREATE TRIGGER imageProductOnUpdate
                            BEFORE UPDATE
                            ON product
                            FOR EACH ROW
                            BEGIN
                                IF(new.image_link is null or new.image_link=\'\') THEN
                                    SET new.image_link=\'https://a2mo-197c6.kxcdn.com/wp-content/uploads/2021/10/placeholder1.png\';
                                END IF;
                            END;');
        $this->addSql('CREATE TRIGGER removeQteProductFromPurchase 
                            AFTER INSERT ON `purchase` FOR EACH ROW
                            UPDATE product SET quantity_stock = quantity_stock - NEW.quantity
                            WHERE product.id = NEW.product_id');
        $this->addSql('CREATE TRIGGER verifDispoProduit 
                            BEFORE INSERT ON `purchase` FOR EACH ROW
                            IF((SELECT quantity_stock FROM product WHERE id = NEW.product_id) < NEW.quantity) THEN
                                    SIGNAL SQLSTATE "45000"
                                    SET MESSAGE_TEXT = "Stock vide ou insuffisant, erreur creation purchase";
                            END IF;');
    }
}
