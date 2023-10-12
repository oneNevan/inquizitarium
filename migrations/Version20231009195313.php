<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231009195313 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create tables to store quiz results';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE quiz_result_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE quiz_result_answer_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE quiz_result_question_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE quiz_result (id INT NOT NULL, quiz_id BYTEA NOT NULL, is_passed BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FE2E314A853CD175 ON quiz_result (quiz_id)');
        $this->addSql('COMMENT ON COLUMN quiz_result.quiz_id IS \'(DC2Type:uuid_binary)\'');
        $this->addSql('COMMENT ON COLUMN quiz_result.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE quiz_result_answer (id INT NOT NULL, question_id INT NOT NULL, text VARCHAR(255) NOT NULL, is_correct BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_14FBA3681E27F6BF ON quiz_result_answer (question_id)');
        $this->addSql('CREATE TABLE quiz_result_question (id INT NOT NULL, result_id INT NOT NULL, text VARCHAR(255) NOT NULL, is_answer_accepted BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2AF6ECFC7A7B643 ON quiz_result_question (result_id)');
        $this->addSql('ALTER TABLE quiz_result_answer ADD CONSTRAINT FK_14FBA3681E27F6BF FOREIGN KEY (question_id) REFERENCES quiz_result_question (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quiz_result_question ADD CONSTRAINT FK_2AF6ECFC7A7B643 FOREIGN KEY (result_id) REFERENCES quiz_result (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE quiz_result_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE quiz_result_answer_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE quiz_result_question_id_seq CASCADE');
        $this->addSql('ALTER TABLE quiz_result_answer DROP CONSTRAINT FK_14FBA3681E27F6BF');
        $this->addSql('ALTER TABLE quiz_result_question DROP CONSTRAINT FK_2AF6ECFC7A7B643');
        $this->addSql('DROP TABLE quiz_result');
        $this->addSql('DROP TABLE quiz_result_answer');
        $this->addSql('DROP TABLE quiz_result_question');
    }
}
