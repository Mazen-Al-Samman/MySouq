<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%field_assign}}`.
 */
class m210416_000454_create_field_assign_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%field_assign}}', [
            'id' => $this->primaryKey(),
            'field_id' => $this->integer()->notNull(),
            'cat_id' => $this->integer()->notNull(),
            'country_id' => $this->integer()->notNull(),
            'label' => $this->string()->notNull()
        ]);
        $this->addForeignKey('field_assign_cat_id_foreign', 'field_assign', 'cat_id', 'category', 'id', 'cascade', 'cascade');
        $this->addForeignKey('field_assign_country_id_foreign', 'field_assign', 'country_id', 'country', 'id', 'cascade', 'cascade');
        $this->addForeignKey('field_assign_field_id_foreign', 'field_assign', 'field_id', 'field', 'id', 'cascade', 'cascade');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%field_assign}}');
    }
}
