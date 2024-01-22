<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%timeline}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%user}}`
 */
class m200524_120538_create_timeline_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%timeline}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'title' => $this->string()->notNull(),
            'start' => $this->date()->notNull(),
            'end' => $this->date()->notNull(),
            'status' => $this->string()->notNull(),
            'created_at' => $this->integer()->defaultValue(null),
            'created_by' => $this->integer()->defaultValue(null),
            'updated_at' => $this->integer()->defaultValue(null),
            'updated_by' => $this->integer()->defaultValue(null),
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            '{{%idx-timeline-user_id}}',
            '{{%timeline}}',
            'user_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-timeline-user_id}}',
            '{{%timeline}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-timeline-user_id}}',
            '{{%timeline}}'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            '{{%idx-timeline-user_id}}',
            '{{%timeline}}'
        );

        $this->dropTable('{{%timeline}}');
    }
}
