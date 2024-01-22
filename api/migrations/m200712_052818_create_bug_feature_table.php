<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%bug_feature}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%user}}`
 */
class m200712_052818_create_bug_feature_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%bug_feature}}', [
            'id' => $this->primaryKey(),
            'reporter_user_id' => $this->integer()->notNull(),
            'qa_user_id' => $this->integer()->defaultValue(null),
            'developer_user_id' => $this->integer()->defaultValue(null),
            'title' => $this->string()->notNull(),
            'description' => $this->text()->defaultValue(null),
            'jira_number' => $this->string()->defaultValue(null),
            'fix_status' => $this->string()->notNull(),
            'priority' => $this->string()->notNull(),
            'status' => $this->string()->notNull(),
            'type' => $this->string()->notNull(),
            'created_at' => $this->integer()->defaultValue(null),
            'created_by' => $this->integer()->defaultValue(null),
            'updated_at' => $this->integer()->defaultValue(null),
            'updated_by' => $this->integer()->defaultValue(null),
            'deleted_at' => $this->integer()->defaultValue(null),
            'deleted_by' => $this->integer()->defaultValue(null),
        ]);

        // creates index for column `reporter_user_id`
        $this->createIndex(
            '{{%idx-bug_feature-reporter_user_id}}',
            '{{%bug_feature}}',
            'reporter_user_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-bug_feature-reporter_user_id}}',
            '{{%bug_feature}}',
            'reporter_user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        // creates index for column `qa_user_id`
        $this->createIndex(
            '{{%idx-bug_feature-qa_user_id}}',
            '{{%bug_feature}}',
            'qa_user_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-bug_feature-qa_user_id}}',
            '{{%bug_feature}}',
            'qa_user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        // creates index for column `developer_user_id`
        $this->createIndex(
            '{{%idx-bug_feature-developer_user_id}}',
            '{{%bug_feature}}',
            'developer_user_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-bug_feature-developer_user_id}}',
            '{{%bug_feature}}',
            'developer_user_id',
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
            '{{%fk-bug_feature-reporter_user_id}}',
            '{{%bug_feature}}'
        );

        // drops index for column `reporter_user_id`
        $this->dropIndex(
            '{{%idx-bug_feature-reporter_user_id}}',
            '{{%bug_feature}}'
        );

        $this->dropForeignKey(
            '{{%fk-bug_feature-qa_user_id}}',
            '{{%bug_feature}}'
        );

        $this->dropIndex(
            '{{%idx-bug_feature-qa_user_id}}',
            '{{%bug_feature}}'
        );

        $this->dropForeignKey(
            '{{%fk-bug_feature-developer_user_id}}',
            '{{%bug_feature}}'
        );

        $this->dropIndex(
            '{{%idx-bug_feature-developer_user_id}}',
            '{{%bug_feature}}'
        );

        $this->dropTable('{{%bug_feature}}');
    }
}
