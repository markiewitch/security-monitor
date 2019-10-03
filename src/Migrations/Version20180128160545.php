<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180128160545 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE checks (id INT AUTO_INCREMENT NOT NULL, project_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', created_at DATETIME NOT NULL, finished_at DATETIME DEFAULT NULL, vulnerabilities JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', vulnerabilities_count INT DEFAULT NULL, was_successful TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE projects CHANGE uuid uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE checks');
        $this->addSql('ALTER TABLE projects CHANGE uuid uuid CHAR(36) NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:uuid)\'');
    }
}
