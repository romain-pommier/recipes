<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191110204042 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE meal_style_recipe (meal_style_id INT NOT NULL, recipe_id INT NOT NULL, INDEX IDX_1343BEF9DEDF7B06 (meal_style_id), INDEX IDX_1343BEF959D8A214 (recipe_id), PRIMARY KEY(meal_style_id, recipe_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE meal_style_recipe ADD CONSTRAINT FK_1343BEF9DEDF7B06 FOREIGN KEY (meal_style_id) REFERENCES meal_style (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE meal_style_recipe ADD CONSTRAINT FK_1343BEF959D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE meal_style_recipe');
    }
}
