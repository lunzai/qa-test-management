<?php

use yii\db\Migration;

/**
 * Handles dropping columns from table `{{%test_case}}`.
 */
class m200417_172411_drop_count_column_from_test_case_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%test_case}}', 'total_count');
        $this->dropColumn('{{%test_case}}', 'passed_count');
        $this->dropColumn('{{%test_case}}', 'failed_count');
        $this->dropColumn('{{%test_case}}', 'pending_count');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%test_case}}', 'total_count', $this->integer()->notNull()->defaultValue(0));
        $this->addColumn('{{%test_case}}', 'passed_count', $this->integer()->notNull()->defaultValue(0));
        $this->addColumn('{{%test_case}}', 'failed_count', $this->integer()->notNull()->defaultValue(0));
        $this->addColumn('{{%test_case}}', 'pending_count', $this->integer()->notNull()->defaultValue(0));
    }
}
