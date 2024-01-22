<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%issue}}`.
 */
class m200410_122800_add_status_column_to_issue_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%issue}}', 'status', $this->string()->notNull()->after('lark_url'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%issue}}', 'status');
    }
}
