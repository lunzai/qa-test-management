<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%test_result}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%test_case}}`
 * - `{{%user}}`
 */
class m200301_143225_create_test_result_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%test_result}}', [
            'id' => $this->primaryKey(),
            'test_case_id' => $this->integer()->notNull(),
            'tester_user_id' => $this->integer()->notNull(),
            'version' => $this->string()->defaultValue(null),
            'platform' => $this->string()->defaultValue(null),
            'actual_result' => $this->text()->notNull(),
            'test_status' => $this->string()->notNull(),
            'created_at' => $this->integer()->defaultValue(null),
            'created_by' => $this->integer()->defaultValue(null),
            'updated_at' => $this->integer()->defaultValue(null),
            'updated_by' => $this->integer()->defaultValue(null),
        ]);

        // creates index for column `test_case_id`
        $this->createIndex(
            '{{%idx-test_result-test_case_id}}',
            '{{%test_result}}',
            'test_case_id'
        );

        // add foreign key for table `{{%test_case}}`
        $this->addForeignKey(
            '{{%fk-test_result-test_case_id}}',
            '{{%test_result}}',
            'test_case_id',
            '{{%test_case}}',
            'id',
            'CASCADE'
        );

        // creates index for column `tester_user_id`
        $this->createIndex(
            '{{%idx-test_result-tester_user_id}}',
            '{{%test_result}}',
            'tester_user_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-test_result-tester_user_id}}',
            '{{%test_result}}',
            'tester_user_id',
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
        // drops foreign key for table `{{%test_case}}`
        $this->dropForeignKey(
            '{{%fk-test_result-test_case_id}}',
            '{{%test_result}}'
        );

        // drops index for column `test_case_id`
        $this->dropIndex(
            '{{%idx-test_result-test_case_id}}',
            '{{%test_result}}'
        );

        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-test_result-tester_user_id}}',
            '{{%test_result}}'
        );

        // drops index for column `tester_user_id`
        $this->dropIndex(
            '{{%idx-test_result-tester_user_id}}',
            '{{%test_result}}'
        );

        $this->dropTable('{{%test_result}}');
    }
}
