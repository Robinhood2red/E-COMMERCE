<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260302084443 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE alpha_camp (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, category_id INT NOT NULL, INDEX IDX_EACAFF5312469DE2 (category_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE alpha_camp ADD CONSTRAINT FK_EACAFF5312469DE2 FOREIGN KEY (category_id) REFERENCES categorie (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE alpha_camp DROP FOREIGN KEY FK_EACAFF5312469DE2');
        $this->addSql('DROP TABLE alpha_camp');
    }
}
