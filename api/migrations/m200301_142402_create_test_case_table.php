<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%test_case}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%issue}}`
 */
class m200301_142402_create_test_case_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%test_case}}', [
            'id' => $this->primaryKey(),
            'issue_id' => $this->integer()->notNull(),
            'description' => $this->text()->notNull(),
            'platform' => $this->string()->defaultValue(null),
            'pre_condition' => $this->string()->defaultValue(null),
            'replicate_step' => $this->text()->notNull(),
            'expected_result' => $this->text()->notNull(),
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

        // creates index for column `issue_id`
        $this->createIndex(
            '{{%idx-test_case-issue_id}}',
            '{{%test_case}}',
            'issue_id'
        );

        // add foreign key for table `{{%issue}}`
        $this->addForeignKey(
            '{{%fk-test_case-issue_id}}',
            '{{%test_case}}',
            'issue_id',
            '{{%issue}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%issue}}`
        $this->dropForeignKey(
            '{{%fk-test_case-issue_id}}',
            '{{%test_case}}'
        );

        // drops index for column `issue_id`
        $this->dropIndex(
            '{{%idx-test_case-issue_id}}',
            '{{%test_case}}'
        );

        $this->dropTable('{{%test_case}}');
    }
}
