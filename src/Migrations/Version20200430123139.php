<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200430123139 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE livre ADD dispo TINYINT(1) DEFAULT NULL, CHANGE editeur_id editeur_id INT NOT NULL, CHANGE auteur_id auteur_id INT NOT NULL, CHANGE genre_id genre_id INT NOT NULL, CHANGE annee annee INT DEFAULT NULL, CHANGE langue langue VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE livre DROP dispo, CHANGE genre_id genre_id INT DEFAULT NULL, CHANGE editeur_id editeur_id INT DEFAULT NULL, CHANGE auteur_id auteur_id INT DEFAULT NULL, CHANGE annee annee INT NOT NULL, CHANGE langue langue VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
