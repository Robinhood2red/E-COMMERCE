<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260326122345 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE add_product_history ADD CONSTRAINT FK_EDEB7BDE4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE alpha_camp ADD CONSTRAINT FK_EACAFF5312469DE2 FOREIGN KEY (category_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE `order` ADD is_payment_completed TINYINT NOT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993988BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE order_products ADD CONSTRAINT FK_5242B8EBA35F2858 FOREIGN KEY (_order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE order_products ADD CONSTRAINT FK_5242B8EB4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADBCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE product_alpha_camp ADD CONSTRAINT FK_79ADA0784584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_alpha_camp ADD CONSTRAINT FK_79ADA078E02E83EF FOREIGN KEY (alpha_camp_id) REFERENCES alpha_camp (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE add_product_history DROP FOREIGN KEY FK_EDEB7BDE4584665A');
        $this->addSql('ALTER TABLE alpha_camp DROP FOREIGN KEY FK_EACAFF5312469DE2');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993988BAC62AF');
        $this->addSql('ALTER TABLE `order` DROP is_payment_completed');
        $this->addSql('ALTER TABLE order_products DROP FOREIGN KEY FK_5242B8EBA35F2858');
        $this->addSql('ALTER TABLE order_products DROP FOREIGN KEY FK_5242B8EB4584665A');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADBCF5E72D');
        $this->addSql('ALTER TABLE product_alpha_camp DROP FOREIGN KEY FK_79ADA0784584665A');
        $this->addSql('ALTER TABLE product_alpha_camp DROP FOREIGN KEY FK_79ADA078E02E83EF');
    }
}
