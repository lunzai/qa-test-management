<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%country}}`.
 */
class m200609_033700_add_status_column_to_country_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%country}}', 'status', $this->string()->notNull()->defaultValue('Active')->after('iso3'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%country}}', 'status');
    }
}
