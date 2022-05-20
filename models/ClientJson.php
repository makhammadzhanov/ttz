<?php

namespace app\models;

use Yii;
use app\dataproviders\JsonDataProvider;
use yii\base\Model;

/**
 * @property int $id
 * @property JsonDataProvider $dataProvider
 */
class ClientJson extends Model
{
    public $id;
    public string $fullname;
    public string $email;
    public string $phone_number;
    private JsonDataProvider $dataProvider;
    public bool $isNewRecord = false;

    /**
     * {@inheritdoc}
     */
    public function fields(): array
    {
        $fields = parent::fields();
        unset($fields['isNewRecord']);

        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        $this->dataProvider = new JsonDataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['fullname', 'email', 'phone_number'], 'required'],
            [['fullname', 'email', 'phone_number'], 'string', 'max' => 255],
            [['email'], 'email'],
        ];
    }

    /**
     * @param $runValidation
     * @param $attributeNames
     * @return bool
     */
    public function save($runValidation = true, $attributeNames = null): bool
    {
        if ($this->validate()) {
            return $this->dataProvider->save($this);
        }

        return false;
    }

    /**
     * @param $condition
     * @return ClientJson|null
     */
    public static function findOne($condition): ?ClientJson
    {
        return JsonDataProvider::findOne($condition);
    }

    /**
     * @param array $post
     * @return ClientJson|null
     */
    public static function create(array $post): ?ClientJson
    {
        $model = new self;
        $model->isNewRecord = true;
        $model->attributes = $post;

        if ($model->save()) {
            $last_id = JsonDataProvider::getLastId();
            return self::findOne($last_id);
        }

        return null;
    }

    /**
     * @param int $id
     * @param array $post
     * @return ClientJson|null
     */
    public static function update(int $id, array $post): ?ClientJson
    {
        $model = JsonDataProvider::findOne($id);

        if ($model !== null) {
            $model->attributes = $post;
            if ($model->save()) {
                return $model;
            }
        }

        return null;
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
