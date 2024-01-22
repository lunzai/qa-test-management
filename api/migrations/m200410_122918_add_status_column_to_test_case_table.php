<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%test_case}}`.
 */
class m200410_122918_add_status_column_to_test_case_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%test_case}}', 'status', $this->string()->notNull()->after('expected_result'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%test_case}}', 'status');
    }
}
