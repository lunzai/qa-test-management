<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%issue}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%group}}`
 * - `{{%user}}`
 */
class m200301_141028_create_issue_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%issue}}', [
            'id' => $this->primaryKey(),
            'group_id' => $this->integer()->notNull(),
            'qa_user_id' => $this->integer()->defaultValue(null),
            'developer_user_id' => $this->integer()->defaultValue(null),
            'name' => $this->string()->notNull(),
            'description' => $this->text()->defaultValue(null),
            'jira_number' => $this->string(60)->defaultValue(null),
            'jira_url' => $this->string()->defaultValue(null),
            'lark_url' => $this->string()->defaultValue(null),
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

        // creates index for column `group_id`
        $this->createIndex(
            '{{%idx-issue-group_id}}',
            '{{%issue}}',
            'group_id'
        );

        // add foreign key for table `{{%group}}`
        $this->addForeignKey(
            '{{%fk-issue-group_id}}',
            '{{%issue}}',
            'group_id',
            '{{%group}}',
            'id',
            'CASCADE'
        );

        // creates index for column `qa_user_id`
        $this->createIndex(
            '{{%idx-issue-qa_user_id}}',
            '{{%issue}}',
            'qa_user_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-issue-qa_user_id}}',
            '{{%issue}}',
            'qa_user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
        
        // creates index for column `qa_user_id`
        $this->createIndex(
            '{{%idx-issue-developer_user_id}}',
            '{{%issue}}',
            'developer_user_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-issue-developer_user_id}}',
            '{{%issue}}',
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
        // drops foreign key for table `{{%group}}`
        $this->dropForeignKey(
            '{{%fk-issue-group_id}}',
            '{{%issue}}'
        );

        // drops index for column `group_id`
        $this->dropIndex(
            '{{%idx-issue-group_id}}',
            '{{%issue}}'
        );

        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-issue-qa_user_id}}',
            '{{%issue}}'
        );

        // drops index for column `qa_user_id`
        $this->dropIndex(
            '{{%idx-issue-qa_user_id}}',
            '{{%issue}}'
        );
        
        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-issue-developer_user_id}}',
            '{{%issue}}'
        );

        // drops index for column `developer_user_id`
        $this->dropIndex(
            '{{%idx-issue-developer_user_id}}',
            '{{%issue}}'
        );
        

        $this->dropTable('{{%issue}}');
    }
}
