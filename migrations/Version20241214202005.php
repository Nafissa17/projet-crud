<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241214202005 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE serie CHANGE nombre_saisons nombre_saisons INT DEFAULT NULL, CHANGE synopsis synopsis LONGTEXT DEFAULT NULL, CHANGE date_diffusion date_diffusion DATE DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_4B5C076CD94388BD ON serie_genre (serie_id)');
        $this->addSql('ALTER TABLE serie_genre RENAME INDEX fk_serie_genre_genre TO IDX_4B5C076C4296D31F');
        $this->addSql('CREATE INDEX IDX_2F2DA414D94388BD ON serie_producteur (serie_id)');
        $this->addSql('ALTER TABLE serie_producteur RENAME INDEX fk_serie_producteur_producteur TO IDX_2F2DA414AB9BB300');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE serie CHANGE nombre_saisons nombre_saisons INT NOT NULL, CHANGE synopsis synopsis TEXT DEFAULT NULL, CHANGE date_diffusion date_diffusion DATE NOT NULL');
        $this->addSql('ALTER TABLE serie_genre DROP FOREIGN KEY FK_4B5C076CD94388BD');
        $this->addSql('ALTER TABLE serie_genre DROP FOREIGN KEY FK_4B5C076C4296D31F');
        $this->addSql('DROP INDEX IDX_4B5C076CD94388BD ON serie_genre');
        $this->addSql('ALTER TABLE serie_genre RENAME INDEX idx_4b5c076c4296d31f TO FK_Serie_Genre_Genre');
        $this->addSql('ALTER TABLE serie_producteur DROP FOREIGN KEY FK_2F2DA414D94388BD');
        $this->addSql('ALTER TABLE serie_producteur DROP FOREIGN KEY FK_2F2DA414AB9BB300');
        $this->addSql('DROP INDEX IDX_2F2DA414D94388BD ON serie_producteur');
        $this->addSql('ALTER TABLE serie_producteur RENAME INDEX idx_2f2da414ab9bb300 TO FK_Serie_Producteur_Producteur');
    }
}
