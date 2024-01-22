<?php

use yii\db\Migration;

/**
 * Class m200418_181524_change_textarea_to_mediumtext
 */
class m200418_181524_change_textarea_to_mediumtext extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%issue}}', 'description', 'LONGTEXT DEFAULT NULL');
        $this->alterColumn('{{%test_case}}', 'description', 'LONGTEXT NOT NULL');
        $this->alterColumn('{{%test_case}}', 'replicate_step', 'LONGTEXT DEFAULT NULL');
        $this->alterColumn('{{%test_case}}', 'expected_result', 'LONGTEXT DEFAULT NULL');
        $this->alterColumn('{{%test_result}}', 'actual_result', 'LONGTEXT DEFAULT NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200418_181524_change_textarea_to_mediumtext cannot be reverted.\n";

        return false;
    }
    */
}
