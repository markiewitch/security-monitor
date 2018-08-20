<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20180820163601 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
                       'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE packages (id INT AUTO_INCREMENT NOT NULL, vendor VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX unique_package_name (vendor, name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE package_references (project_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\', package_id INT NOT NULL, first_seen_on DATETIME NOT NULL, last_seen_on DATETIME NOT NULL, is_installed TINYINT(1) NOT NULL, INDEX IDX_9302D869166D1F9C (project_id), INDEX IDX_9302D869F44CABFF (package_id), PRIMARY KEY(project_id, package_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE package_references ADD CONSTRAINT FK_9302D869166D1F9C FOREIGN KEY (project_id) REFERENCES projects (uuid)');
        $this->addSql('ALTER TABLE package_references ADD CONSTRAINT FK_9302D869F44CABFF FOREIGN KEY (package_id) REFERENCES packages (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
                       'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE package_references DROP FOREIGN KEY FK_9302D869F44CABFF');
        $this->addSql('DROP TABLE packages');
        $this->addSql('DROP TABLE package_references');
    }
}
