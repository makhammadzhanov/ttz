<?php

namespace app\models;

use Yii;
use app\dataproviders\XlsxDataProvider;
use yii\base\Model;

/**
 * @property int $id
 * @property XlsxDataProvider $dataProvider
 */
class ClientXlsx extends Model
{
    public $id;
    public string $fullname;
    public string $email;
    public string $phone_number;
    private XlsxDataProvider $dataProvider;
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
        $this->dataProvider = new XlsxDataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['fullname', 'email', 'phone_number'], 'required'],
            [['fullname', 'email'], 'string', 'max' => 255],
            [['phone_number'], 'number'],
            [['email'], 'email'],
        ];
    }

    /**
     * @param bool $runValidation
     * @param $attributeNames
     * @return bool
     */
    public function save(bool $runValidation = true, $attributeNames = null): bool
    {
        if ($this->validate()) {
            return $this->dataProvider->save($this);
        }

        return false;
    }

    /**
     * @param $condition
     * @return ClientXlsx|null
     */
    public static function findOne($condition): ?ClientXlsx
    {
        return XlsxDataProvider::findOne($condition);
    }

    /**
     * @param array $post
     * @return ClientXlsx|null
     */
    public static function create(array $post): ?ClientXlsx
    {
        $model = new self;
        $model->isNewRecord = true;
        $model->attributes = $post;

        if ($model->save()) {
            $last_id = XlsxDataProvider::getLastId();
            return self::findOne($last_id);
        }

        return null;
    }

    /**
     * @param int $id
     * @param array $post
     * @return ClientXlsx|null
     */
    public static function update(int $id, array $post): ?ClientXlsx
    {
        $model = XlsxDataProvider::findOne($id);

        if ($model !== null) {
            $model->attributes = $post;
            if ($model->save()) {
                return $model;
            }
        }

        return null;
    }

    /**
     * @param int $id
     * @return bool
     */
    public static function delete(int $id): bool
    {
        return XlsxDataProvider::delete($id);
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
