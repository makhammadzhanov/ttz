<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%clients}}`.
 */
class m220520_045513_create_clients_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('{{%clients}}', [
            'id' => $this->primaryKey(),
            'fullname' => $this->string()->notNull()->unique(),
            'email' => $this->string()->notNull(),
            'phone_number' => $this->string()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('{{%clients}}');
    }
}
