<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user}}`.
 */
class m200229_062119_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'email' => $this->string()->unique()->notNull(),
            'display_name' => $this->string()->notNull(),
            'job_role' => $this->string()->defaultValue(null),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->defaultValue(null)->unique(),
            'email_verification_token' => $this->string()->defaultValue(null)->unique(),
            'email_verified_at' => $this->integer()->defaultValue(null),
            'jwt_token' => $this->string()->defaultValue(null)->unique(),
            'jwt_token_expired_at' => $this->integer()->defaultValue(null),
            'rate_limit_allowance' => $this->integer()->defaultValue(null),
            'rate_limit_allowance_updated_at' => $this->integer()->defaultValue(null),
            'status' => $this->string()->defaultValue(null),
            'created_at' => $this->integer()->defaultValue(null),
            'updated_at' => $this->integer()->defaultValue(null),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}
