<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%bug_feature_follow_up}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%bug_feature}}`
 * - `{{%user}}`
 */
class m200720_103847_create_bug_feature_follow_up_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%bug_feature_follow_up}}', [
            'id' => $this->primaryKey(),
            'bug_feature_id' => $this->integer()->notNull(),
            'actor_user_id' => $this->integer()->defaultValue(null),
            'status' => $this->string()->notNull(),
            'description' => $this->text()->defaultValue(null),
            'due_at' => $this->integer()->defaultValue(null),
            'is_resolved' => $this->boolean()->defaultValue(null),
            'resolved_at' => $this->integer()->defaultValue(null),
            'resolved_by' => $this->integer()->defaultValue(null),
            'resolved_detail' => $this->string()->defaultValue(null),
            'created_at' => $this->integer()->defaultValue(null),
            'created_by' => $this->integer()->defaultValue(null),
            'updated_at' => $this->integer()->defaultValue(null),
            'updated_by' => $this->integer()->defaultValue(null),
            'deleted_at' => $this->integer()->defaultValue(null),
            'deleted_by' => $this->integer()->defaultValue(null),
        ]);

        // creates index for column `bug_feature_id`
        $this->createIndex(
            '{{%idx-bug_feature_follow_up-bug_feature_id}}',
            '{{%bug_feature_follow_up}}',
            'bug_feature_id'
        );

        // add foreign key for table `{{%bug_feature}}`
        $this->addForeignKey(
            '{{%fk-bug_feature_follow_up-bug_feature_id}}',
            '{{%bug_feature_follow_up}}',
            'bug_feature_id',
            '{{%bug_feature}}',
            'id',
            'CASCADE'
        );

        // creates index for column `actor_user_id`
        $this->createIndex(
            '{{%idx-bug_feature_follow_up-actor_user_id}}',
            '{{%bug_feature_follow_up}}',
            'actor_user_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-bug_feature_follow_up-actor_user_id}}',
            '{{%bug_feature_follow_up}}',
            'actor_user_id',
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
        // drops foreign key for table `{{%bug_feature}}`
        $this->dropForeignKey(
            '{{%fk-bug_feature_follow_up-bug_feature_id}}',
            '{{%bug_feature_follow_up}}'
        );

        // drops index for column `bug_feature_id`
        $this->dropIndex(
            '{{%idx-bug_feature_follow_up-bug_feature_id}}',
            '{{%bug_feature_follow_up}}'
        );

        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-bug_feature_follow_up-actor_user_id}}',
            '{{%bug_feature_follow_up}}'
        );

        // drops index for column `actor_user_id`
        $this->dropIndex(
            '{{%idx-bug_feature_follow_up-actor_user_id}}',
            '{{%bug_feature_follow_up}}'
        );

        $this->dropTable('{{%bug_feature_follow_up}}');
    }
}
