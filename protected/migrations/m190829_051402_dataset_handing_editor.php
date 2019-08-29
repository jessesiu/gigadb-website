<?php

class m190829_051402_dataset_handing_editor extends CDbMigration
{
	public function up()
	{
        $this->execute("ALTER TABLE dataset ADD COLUMN handing_editor varchar(20)");
	}

	public function down()
	{
        $this->execute("ALTER TABLE dataset DROP COLUMN handing_editor");
		echo "m190829_051402_dataset_handing_editor does not support migration down.\n";
		return false;
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}