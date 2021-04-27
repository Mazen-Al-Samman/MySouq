<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%option}}`.
 */
class m210416_000512_create_option_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%option}}', [
            'id' => $this->primaryKey(),
            'field_id' => $this->integer()->notNull(),
            'title' => $this->string()->notNull()
        ]);
        $this->addForeignKey('option_field_id_foreign', 'option', 'field_id', 'field', 'id', 'cascade', 'cascade');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%option}}');
    }
}
