<?php

use yii\db\Migration;

/**
 * Class m210417_214521_add_user_role_to_user_table
 */
class m210417_214521_add_user_role_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'role', $this->integer()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'role');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210417_214521_add_user_role_to_user_table cannot be reverted.\n";

        return false;
    }
    */
}
