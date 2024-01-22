<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%test_result}}`.
 */
class m200410_122950_add_status_column_to_test_result_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%test_result}}', 'status', $this->string()->notNull()->after('actual_result'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%test_result}}', 'status');
    }
}
