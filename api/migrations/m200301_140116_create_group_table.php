<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%group}}`.
 */
class m200301_140116_create_group_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%group}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'status' => $this->string()->notNull(),
            'test_status' => $this->string()->notNull(),
            'total_count' => $this->integer()->notNull()->defaultValue(0),
            'passed_count' => $this->integer()->notNull()->defaultValue(0),
            'failed_count' => $this->integer()->notNull()->defaultValue(0),
            'pending_count' => $this->integer()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->defaultValue(null),
            'created_by' => $this->integer()->defaultValue(null),
            'updated_at' => $this->integer()->defaultValue(null),
            'updated_by' => $this->integer()->defaultValue(null),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%group}}');
    }
}
