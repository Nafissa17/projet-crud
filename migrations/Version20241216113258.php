<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241216113258 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE producteur DROP prenom');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE producteur ADD prenom VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE serie_genre DROP FOREIGN KEY FK_4B5C076CD94388BD');
        $this->addSql('ALTER TABLE serie_genre DROP FOREIGN KEY FK_4B5C076C4296D31F');
        $this->addSql('ALTER TABLE serie_producteur DROP FOREIGN KEY FK_2F2DA414D94388BD');
        $this->addSql('ALTER TABLE serie_producteur DROP FOREIGN KEY FK_2F2DA414AB9BB300');
    }
}
