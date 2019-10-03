<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180128162048 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE checks CHANGE project_id project_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\'');
        $this->addSql('ALTER TABLE checks ADD CONSTRAINT FK_9F8C0079166D1F9C FOREIGN KEY (project_id) REFERENCES projects (uuid)');
        $this->addSql('CREATE INDEX IDX_9F8C0079166D1F9C ON checks (project_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE checks DROP FOREIGN KEY FK_9F8C0079166D1F9C');
        $this->addSql('DROP INDEX IDX_9F8C0079166D1F9C ON checks');
        $this->addSql('ALTER TABLE checks CHANGE project_id project_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary_ordered_time)\'');
    }
}
