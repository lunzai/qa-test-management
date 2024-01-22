<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%file}}`.
 */
class m200423_123809_create_file_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%file}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'original_name' => $this->string()->notNull(),
            'extension' => $this->string()->defaultValue(null),
            'path_prefix' => $this->string()->defaultValue(null),
            'file_path' => $this->string()->notNull(),
            'absolute_path' => $this->string()->notNull(),
            'size' => $this->integer()->notNull()->defaultValue(0),
            'content_type' => $this->string()->defaultValue(null),
            'created_at' => $this->integer()->defaultValue(null),
            'created_by' => $this->integer()->defaultValue(null),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%file}}');
    }
}
