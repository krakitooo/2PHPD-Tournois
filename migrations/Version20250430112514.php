<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250430112514 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE sport_match (id INT AUTO_INCREMENT NOT NULL, tournament_id INT DEFAULT NULL, INDEX IDX_CE27A41C33D1A3E7 (tournament_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE tournament (id INT AUTO_INCREMENT NOT NULL, winner_id INT DEFAULT NULL, tournament_name VARCHAR(255) NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, location VARCHAR(255) DEFAULT NULL, description LONGTEXT NOT NULL, max_participants INT NOT NULL, status VARCHAR(255) NOT NULL, sport VARCHAR(255) NOT NULL, INDEX IDX_BD5FB8D95DFCD4B8 (winner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE tournament_user (tournament_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_BA1E647733D1A3E7 (tournament_id), INDEX IDX_BA1E6477A76ED395 (user_id), PRIMARY KEY(tournament_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, last_name VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, email_address VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sport_match ADD CONSTRAINT FK_CE27A41C33D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournament (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tournament ADD CONSTRAINT FK_BD5FB8D95DFCD4B8 FOREIGN KEY (winner_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tournament_user ADD CONSTRAINT FK_BA1E647733D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournament (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tournament_user ADD CONSTRAINT FK_BA1E6477A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE sport_match DROP FOREIGN KEY FK_CE27A41C33D1A3E7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tournament DROP FOREIGN KEY FK_BD5FB8D95DFCD4B8
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tournament_user DROP FOREIGN KEY FK_BA1E647733D1A3E7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tournament_user DROP FOREIGN KEY FK_BA1E6477A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE sport_match
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE tournament
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE tournament_user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE `user`
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
