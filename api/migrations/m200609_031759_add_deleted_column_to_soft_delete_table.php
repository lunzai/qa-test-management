<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%soft_delete}}`.
 */
class m200609_031759_add_deleted_column_to_soft_delete_table extends Migration
{
    public $tables = ['group', 'holiday', 'issue', 'test_case', 'test_result', 'timeline', 'user', 'country'];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        foreach ($this->tables as $table) {
            $this->addColumn($table, 'deleted_at', $this->integer()->defaultValue(null)->after($table == 'user' ? 'updated_at' : 'updated_by'));
            $this->addColumn($table, 'deleted_by', $this->integer()->defaultValue(null)->after('deleted_at'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        foreach ($this->tables as $table) {
            $this->dropColumn($table, 'deleted_at');
            $this->dropColumn($table, 'deleted_by');
        }
    }
}
