<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220413145618 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE member (id INT AUTO_INCREMENT NOT NULL, section_id INT DEFAULT NULL, created_by_id INT NOT NULL, updated_by_id INT DEFAULT NULL, first_name VARCHAR(30) NOT NULL, last_name VARCHAR(20) DEFAULT NULL, telephone1 VARCHAR(20) DEFAULT NULL, telephone2 VARCHAR(15) DEFAULT NULL, avatar VARCHAR(255) DEFAULT NULL, address LONGTEXT DEFAULT NULL, is_treasurer TINYINT(1) NOT NULL, is_president TINYINT(1) NOT NULL, created DATETIME NOT NULL, updated DATETIME DEFAULT NULL, INDEX IDX_70E4FA78D823E37A (section_id), INDEX IDX_70E4FA78B03A8386 (created_by_id), INDEX IDX_70E4FA78896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE membership (id INT AUTO_INCREMENT NOT NULL, section_id INT NOT NULL, member_id INT NOT NULL, treasurer_id INT NOT NULL, created_by_id INT NOT NULL, updated_by_id INT DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, comment LONGTEXT DEFAULT NULL, created DATETIME NOT NULL, updated DATETIME DEFAULT NULL, INDEX IDX_86FFD285D823E37A (section_id), INDEX IDX_86FFD2857597D3FE (member_id), INDEX IDX_86FFD28555808438 (treasurer_id), INDEX IDX_86FFD285B03A8386 (created_by_id), INDEX IDX_86FFD285896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE section (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, updated_by_id INT DEFAULT NULL, label VARCHAR(50) NOT NULL, description LONGTEXT DEFAULT NULL, created DATETIME NOT NULL, updated DATETIME DEFAULT NULL, INDEX IDX_2D737AEFB03A8386 (created_by_id), INDEX IDX_2D737AEF896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE member ADD CONSTRAINT FK_70E4FA78D823E37A FOREIGN KEY (section_id) REFERENCES section (id)');
        $this->addSql('ALTER TABLE member ADD CONSTRAINT FK_70E4FA78B03A8386 FOREIGN KEY (created_by_id) REFERENCES admin (id)');
        $this->addSql('ALTER TABLE member ADD CONSTRAINT FK_70E4FA78896DBBDE FOREIGN KEY (updated_by_id) REFERENCES admin (id)');
        $this->addSql('ALTER TABLE membership ADD CONSTRAINT FK_86FFD285D823E37A FOREIGN KEY (section_id) REFERENCES section (id)');
        $this->addSql('ALTER TABLE membership ADD CONSTRAINT FK_86FFD2857597D3FE FOREIGN KEY (member_id) REFERENCES member (id)');
        $this->addSql('ALTER TABLE membership ADD CONSTRAINT FK_86FFD28555808438 FOREIGN KEY (treasurer_id) REFERENCES member (id)');
        $this->addSql('ALTER TABLE membership ADD CONSTRAINT FK_86FFD285B03A8386 FOREIGN KEY (created_by_id) REFERENCES admin (id)');
        $this->addSql('ALTER TABLE membership ADD CONSTRAINT FK_86FFD285896DBBDE FOREIGN KEY (updated_by_id) REFERENCES admin (id)');
        $this->addSql('ALTER TABLE section ADD CONSTRAINT FK_2D737AEFB03A8386 FOREIGN KEY (created_by_id) REFERENCES admin (id)');
        $this->addSql('ALTER TABLE section ADD CONSTRAINT FK_2D737AEF896DBBDE FOREIGN KEY (updated_by_id) REFERENCES admin (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE membership DROP FOREIGN KEY FK_86FFD2857597D3FE');
        $this->addSql('ALTER TABLE membership DROP FOREIGN KEY FK_86FFD28555808438');
        $this->addSql('ALTER TABLE member DROP FOREIGN KEY FK_70E4FA78D823E37A');
        $this->addSql('ALTER TABLE membership DROP FOREIGN KEY FK_86FFD285D823E37A');
        $this->addSql('DROP TABLE member');
        $this->addSql('DROP TABLE membership');
        $this->addSql('DROP TABLE section');
    }
}
