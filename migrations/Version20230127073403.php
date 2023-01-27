<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230127073403 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE off_time (id INT AUTO_INCREMENT NOT NULL, employee_id INT NOT NULL, start_date DATETIME NOT NULL, end_time DATETIME NOT NULL, type VARCHAR(20) NOT NULL, days INT NOT NULL, INDEX IDX_AEB1E7A58C03F15C (employee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(50) NOT NULL, last_name VARCHAR(50) NOT NULL, vacation_days INT NOT NULL, compensatory_time_days INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE off_time ADD CONSTRAINT FK_AEB1E7A58C03F15C FOREIGN KEY (employee_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE off_time DROP FOREIGN KEY FK_AEB1E7A58C03F15C');
        $this->addSql('DROP TABLE off_time');
        $this->addSql('DROP TABLE user');
    }
}
