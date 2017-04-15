<?php

namespace EMA\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170415134804 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("CREATE TABLE notes
                            (
                                id varchar(36) NOT NULL,
                                owner_id varchar(36) NOT NULL,
                                note_text text NOT NULL,
                                PRIMARY KEY(id)
                            )
        ");
    }
    
    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DROP TABLE notes");
    }
}
