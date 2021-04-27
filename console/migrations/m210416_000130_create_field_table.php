<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%field}}`.
 */
class m210416_000130_create_field_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%field}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%field}}');
    }
}
