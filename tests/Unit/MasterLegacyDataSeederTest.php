<?php

namespace Tests\Unit;

use Database\Seeders\MasterLegacyDataSeeder;
use PHPUnit\Framework\TestCase;

class MasterLegacyDataSeederTest extends TestCase
{
    public function test_it_replaces_null_id_with_generated_id_in_insert_statement(): void
    {
        $statement = "INSERT INTO `MASTER` (FILE_NO,ID,ISDELETE) values ('335/2010',null,0);";

        $result = MasterLegacyDataSeeder::rewriteInsertStatementForId($statement, 9999);

        $this->assertStringContainsString("('335/2010',9999,0)", $result);
        $this->assertStringNotContainsString("('335/2010',null,0)", $result);
    }
}
