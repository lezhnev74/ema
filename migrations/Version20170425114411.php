<?php

namespace EMA\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170425114411 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("CREATE TABLE accounts
                            (
                                id varchar(36) NOT NULL,
                                social_provider_id varchar(64) NOT NULL,
                                social_provider_name varchar(10) NOT NULL,
                                PRIMARY KEY(id)
                            )
        ");
        
    }
    
    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DROP TABLE accounts");
    }
}
