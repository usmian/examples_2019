<?php

use yii\db\Migration;

/**
 * Class m171005_032456_create_users_log
 */
class m171005_032456_create_users_log extends Migration
{
    /**
     * @inheritdoc
     */
/*    public function safeUp()
    {

    }*/

    /**
     * @inheritdoc
     */
/*    public function safeDown()
    {
        echo "m171005_032456_create_users_log cannot be reverted.\n";

        return false;
    }*/


    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('users_log', [
            'id' => $this->primaryKey(),
            'user_id' =>  $this->integer()->notNull(),
            'user_ip' => $this->string(32)->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);
    }

    public function down()
    {
        echo "m171005_032456_create_users_log cannot be reverted.\n";

        return false;
    }

}
