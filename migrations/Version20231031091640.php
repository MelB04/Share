<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231031091640 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorie_fichier (categorie_id INT NOT NULL, fichier_id INT NOT NULL, INDEX IDX_C4F5FBBDBCF5E72D (categorie_id), INDEX IDX_C4F5FBBDF915CFE (fichier_id), PRIMARY KEY(categorie_id, fichier_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE categorie_fichier ADD CONSTRAINT FK_C4F5FBBDBCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE categorie_fichier ADD CONSTRAINT FK_C4F5FBBDF915CFE FOREIGN KEY (fichier_id) REFERENCES fichier (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categorie_fichier DROP FOREIGN KEY FK_C4F5FBBDBCF5E72D');
        $this->addSql('ALTER TABLE categorie_fichier DROP FOREIGN KEY FK_C4F5FBBDF915CFE');
        $this->addSql('DROP TABLE categorie_fichier');
    }
}
