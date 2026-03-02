<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260302124718 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, product_description LONGTEXT DEFAULT NULL, price INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE product_alpha_camp (product_id INT NOT NULL, alpha_camp_id INT NOT NULL, INDEX IDX_79ADA0784584665A (product_id), INDEX IDX_79ADA078E02E83EF (alpha_camp_id), PRIMARY KEY (product_id, alpha_camp_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE product_alpha_camp ADD CONSTRAINT FK_79ADA0784584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_alpha_camp ADD CONSTRAINT FK_79ADA078E02E83EF FOREIGN KEY (alpha_camp_id) REFERENCES alpha_camp (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE alpha_camp ADD CONSTRAINT FK_EACAFF5312469DE2 FOREIGN KEY (category_id) REFERENCES categorie (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_alpha_camp DROP FOREIGN KEY FK_79ADA0784584665A');
        $this->addSql('ALTER TABLE product_alpha_camp DROP FOREIGN KEY FK_79ADA078E02E83EF');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_alpha_camp');
        $this->addSql('ALTER TABLE alpha_camp DROP FOREIGN KEY FK_EACAFF5312469DE2');
    }
}
