<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "clients".
 *
 * @property int $id
 * @property string $fullname
 * @property string $email
 * @property string $phone_number
 */
class Client extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'clients';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['fullname', 'email', 'phone_number'], 'required'],
            [['fullname', 'email', 'phone_number'], 'string', 'max' => 255],
            [['fullname'], 'unique'],
            [['email'], 'email'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'fullname' => 'Fullname',
            'email' => 'Email',
            'phone_number' => 'Phone Number',
        ];
    }
}
