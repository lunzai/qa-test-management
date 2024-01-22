<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%holiday}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%country}}`
 */
class m200525_042819_create_holiday_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%holiday}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNUll(),
            'start' => $this->date()->notNull(),
            'end' => $this->date()->notNull(),
            'start_ts' => $this->integer()->notNull(),
            'end_ts' => $this->integer()->notNull(),
            'status' => $this->string()->notNull(),
            'country_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->defaultValue(null),
            'created_by' => $this->integer()->defaultValue(null),
            'updated_at' => $this->integer()->defaultValue(null),
            'updated_by' => $this->integer()->defaultValue(null),
        ]);

        // creates index for column `country_id`
        $this->createIndex(
            '{{%idx-holiday-country_id}}',
            '{{%holiday}}',
            'country_id'
        );

        // add foreign key for table `{{%country}}`
        $this->addForeignKey(
            '{{%fk-holiday-country_id}}',
            '{{%holiday}}',
            'country_id',
            '{{%country}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%country}}`
        $this->dropForeignKey(
            '{{%fk-holiday-country_id}}',
            '{{%holiday}}'
        );

        // drops index for column `country_id`
        $this->dropIndex(
            '{{%idx-holiday-country_id}}',
            '{{%holiday}}'
        );

        $this->dropTable('{{%holiday}}');
    }
}
