<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240527201841 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categorie_fichier (categorie_id INT NOT NULL, fichier_id INT NOT NULL, INDEX IDX_C4F5FBBDBCF5E72D (categorie_id), INDEX IDX_C4F5FBBDF915CFE (fichier_id), PRIMARY KEY(categorie_id, fichier_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(30) NOT NULL, sujet VARCHAR(100) NOT NULL, email VARCHAR(100) NOT NULL, message LONGTEXT NOT NULL, date_envoi DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fichier (id INT AUTO_INCREMENT NOT NULL, proprietaire_id INT NOT NULL, updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', nom_original VARCHAR(255) NOT NULL, nom_serveur VARCHAR(255) NOT NULL, date_envoi DATETIME NOT NULL, extension VARCHAR(3) NOT NULL, taille DOUBLE PRECISION NOT NULL, INDEX IDX_9B76551F76C50E4A (proprietaire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fichier_user (fichier_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_C1C1EA2F915CFE (fichier_id), INDEX IDX_C1C1EA2A76ED395 (user_id), PRIMARY KEY(fichier_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, parent_id INT DEFAULT NULL, title VARCHAR(50) NOT NULL, date_post DATETIME NOT NULL, content LONGTEXT NOT NULL, INDEX IDX_B6BD307FA76ED395 (user_id), INDEX IDX_B6BD307F727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE telecharger (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, fichier_id INT NOT NULL, nb INT DEFAULT NULL, INDEX IDX_E06A3C34A76ED395 (user_id), INDEX IDX_E06A3C34F915CFE (fichier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, date_register DATETIME NOT NULL, lastname VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE categorie_fichier ADD CONSTRAINT FK_C4F5FBBDBCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE categorie_fichier ADD CONSTRAINT FK_C4F5FBBDF915CFE FOREIGN KEY (fichier_id) REFERENCES fichier (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fichier ADD CONSTRAINT FK_9B76551F76C50E4A FOREIGN KEY (proprietaire_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE fichier_user ADD CONSTRAINT FK_C1C1EA2F915CFE FOREIGN KEY (fichier_id) REFERENCES fichier (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fichier_user ADD CONSTRAINT FK_C1C1EA2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F727ACA70 FOREIGN KEY (parent_id) REFERENCES message (id)');
        $this->addSql('ALTER TABLE telecharger ADD CONSTRAINT FK_E06A3C34A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE telecharger ADD CONSTRAINT FK_E06A3C34F915CFE FOREIGN KEY (fichier_id) REFERENCES fichier (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categorie_fichier DROP FOREIGN KEY FK_C4F5FBBDBCF5E72D');
        $this->addSql('ALTER TABLE categorie_fichier DROP FOREIGN KEY FK_C4F5FBBDF915CFE');
        $this->addSql('ALTER TABLE fichier DROP FOREIGN KEY FK_9B76551F76C50E4A');
        $this->addSql('ALTER TABLE fichier_user DROP FOREIGN KEY FK_C1C1EA2F915CFE');
        $this->addSql('ALTER TABLE fichier_user DROP FOREIGN KEY FK_C1C1EA2A76ED395');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FA76ED395');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F727ACA70');
        $this->addSql('ALTER TABLE telecharger DROP FOREIGN KEY FK_E06A3C34A76ED395');
        $this->addSql('ALTER TABLE telecharger DROP FOREIGN KEY FK_E06A3C34F915CFE');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE categorie_fichier');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE fichier');
        $this->addSql('DROP TABLE fichier_user');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE telecharger');
        $this->addSql('DROP TABLE user');
    }
}
