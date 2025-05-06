<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250506220404 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE registration (id INT AUTO_INCREMENT NOT NULL, player_id INT NOT NULL, tournament_id INT NOT NULL, registration_date DATETIME NOT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_62A8A7A799E6F5DF (player_id), INDEX IDX_62A8A7A733D1A3E7 (tournament_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE sport_match (id INT AUTO_INCREMENT NOT NULL, tournament_id INT NOT NULL, player1_id INT NOT NULL, player2_id INT NOT NULL, match_date DATETIME NOT NULL, score_player1 INT NOT NULL, score_player2 INT NOT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_CE27A41C33D1A3E7 (tournament_id), INDEX IDX_CE27A41CC0990423 (player1_id), INDEX IDX_CE27A41CD22CABCD (player2_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE tournament (id INT AUTO_INCREMENT NOT NULL, organizer_id INT NOT NULL, winner_id INT DEFAULT NULL, tournament_name VARCHAR(255) NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, location VARCHAR(255) DEFAULT NULL, description LONGTEXT NOT NULL, max_participants INT NOT NULL, sport VARCHAR(255) NOT NULL, INDEX IDX_BD5FB8D9876C4DDA (organizer_id), INDEX IDX_BD5FB8D95DFCD4B8 (winner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, last_name VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, email_address VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649B08E074E (email_address), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE registration ADD CONSTRAINT FK_62A8A7A799E6F5DF FOREIGN KEY (player_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE registration ADD CONSTRAINT FK_62A8A7A733D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournament (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sport_match ADD CONSTRAINT FK_CE27A41C33D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournament (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sport_match ADD CONSTRAINT FK_CE27A41CC0990423 FOREIGN KEY (player1_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sport_match ADD CONSTRAINT FK_CE27A41CD22CABCD FOREIGN KEY (player2_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tournament ADD CONSTRAINT FK_BD5FB8D9876C4DDA FOREIGN KEY (organizer_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tournament ADD CONSTRAINT FK_BD5FB8D95DFCD4B8 FOREIGN KEY (winner_id) REFERENCES `user` (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE registration DROP FOREIGN KEY FK_62A8A7A799E6F5DF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE registration DROP FOREIGN KEY FK_62A8A7A733D1A3E7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sport_match DROP FOREIGN KEY FK_CE27A41C33D1A3E7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sport_match DROP FOREIGN KEY FK_CE27A41CC0990423
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sport_match DROP FOREIGN KEY FK_CE27A41CD22CABCD
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tournament DROP FOREIGN KEY FK_BD5FB8D9876C4DDA
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tournament DROP FOREIGN KEY FK_BD5FB8D95DFCD4B8
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE registration
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE sport_match
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE tournament
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE `user`
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
