<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240411132534 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, parent_id INT DEFAULT NULL, title VARCHAR(50) NOT NULL, date_post DATETIME NOT NULL, content LONGTEXT NOT NULL, INDEX IDX_B6BD307FA76ED395 (user_id), INDEX IDX_B6BD307F727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F727ACA70 FOREIGN KEY (parent_id) REFERENCES message (id)');
        $this->addSql('ALTER TABLE categorie CHANGE libelle libelle VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE fichier CHANGE extension extension VARCHAR(3) NOT NULL');
        $this->addSql('ALTER TABLE user ADD lastname VARCHAR(255) NOT NULL, ADD firstname VARCHAR(255) NOT NULL, DROP nom, DROP prenom, CHANGE date_inscription date_register DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FA76ED395');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F727ACA70');
        $this->addSql('DROP TABLE message');
        $this->addSql('ALTER TABLE categorie CHANGE libelle libelle VARCHAR(30) NOT NULL');
        $this->addSql('ALTER TABLE fichier CHANGE extension extension VARCHAR(4) NOT NULL');
        $this->addSql('ALTER TABLE user ADD nom VARCHAR(255) NOT NULL, ADD prenom VARCHAR(255) NOT NULL, DROP lastname, DROP firstname, CHANGE date_register date_inscription DATETIME NOT NULL');
    }
}
