<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260304070419 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE add_product_history (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, quantity INT NOT NULL, product_id INT NOT NULL, INDEX IDX_EDEB7BDE4584665A (product_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE alpha_camp (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(180) NOT NULL, category_id INT NOT NULL, INDEX IDX_EACAFF5312469DE2 (category_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(180) NOT NULL, product_description LONGTEXT DEFAULT NULL, price INT NOT NULL, images VARCHAR(180) DEFAULT NULL, stock INT NOT NULL, UNIQUE INDEX UNIQ_D34A04AD5E237E06 (name), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE product_alpha_camp (product_id INT NOT NULL, alpha_camp_id INT NOT NULL, INDEX IDX_79ADA0784584665A (product_id), INDEX IDX_79ADA078E02E83EF (alpha_camp_id), PRIMARY KEY (product_id, alpha_camp_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE add_product_history ADD CONSTRAINT FK_EDEB7BDE4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE alpha_camp ADD CONSTRAINT FK_EACAFF5312469DE2 FOREIGN KEY (category_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE product_alpha_camp ADD CONSTRAINT FK_79ADA0784584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_alpha_camp ADD CONSTRAINT FK_79ADA078E02E83EF FOREIGN KEY (alpha_camp_id) REFERENCES alpha_camp (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user CHANGE lastname lastname VARCHAR(180) NOT NULL, CHANGE firstname firstname VARCHAR(180) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE add_product_history DROP FOREIGN KEY FK_EDEB7BDE4584665A');
        $this->addSql('ALTER TABLE alpha_camp DROP FOREIGN KEY FK_EACAFF5312469DE2');
        $this->addSql('ALTER TABLE product_alpha_camp DROP FOREIGN KEY FK_79ADA0784584665A');
        $this->addSql('ALTER TABLE product_alpha_camp DROP FOREIGN KEY FK_79ADA078E02E83EF');
        $this->addSql('DROP TABLE add_product_history');
        $this->addSql('DROP TABLE alpha_camp');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_alpha_camp');
        $this->addSql('ALTER TABLE user CHANGE lastname lastname VARCHAR(255) NOT NULL, CHANGE firstname firstname VARCHAR(255) NOT NULL');
    }
}
