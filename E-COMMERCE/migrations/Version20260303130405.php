<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260303130405 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE alpha_camp CHANGE name name VARCHAR(180) NOT NULL');
        $this->addSql('ALTER TABLE alpha_camp ADD CONSTRAINT FK_EACAFF5312469DE2 FOREIGN KEY (category_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE product CHANGE name name VARCHAR(180) NOT NULL, CHANGE images images VARCHAR(180) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D34A04AD5E237E06 ON product (name)');
        $this->addSql('ALTER TABLE product_alpha_camp ADD CONSTRAINT FK_79ADA0784584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_alpha_camp ADD CONSTRAINT FK_79ADA078E02E83EF FOREIGN KEY (alpha_camp_id) REFERENCES alpha_camp (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user CHANGE lastname lastname VARCHAR(180) NOT NULL, CHANGE firstname firstname VARCHAR(180) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE alpha_camp DROP FOREIGN KEY FK_EACAFF5312469DE2');
        $this->addSql('ALTER TABLE alpha_camp CHANGE name name VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX UNIQ_D34A04AD5E237E06 ON product');
        $this->addSql('ALTER TABLE product CHANGE name name VARCHAR(255) NOT NULL, CHANGE images images VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE product_alpha_camp DROP FOREIGN KEY FK_79ADA0784584665A');
        $this->addSql('ALTER TABLE product_alpha_camp DROP FOREIGN KEY FK_79ADA078E02E83EF');
        $this->addSql('ALTER TABLE user CHANGE lastname lastname VARCHAR(255) NOT NULL, CHANGE firstname firstname VARCHAR(255) NOT NULL');
    }
}
