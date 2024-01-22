<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%test_case}}`.
 */
class m200412_160006_add_short_description_column_to_test_case_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%test_case}}', 'short_description', $this->string()->notNull()->after('issue_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%test_case}}', 'short_description');
    }
}
