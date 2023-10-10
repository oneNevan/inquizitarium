<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231009222548 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create question pool table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE quiz_question_pool_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE quiz_question_pool (id INT NOT NULL, expression VARCHAR(255) NOT NULL, comparison VARCHAR(5) NOT NULL, answer_options JSON NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE quiz_question_pool_id_seq CASCADE');
        $this->addSql('DROP TABLE quiz_question_pool');
    }
}
