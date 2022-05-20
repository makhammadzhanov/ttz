<?php

namespace app\dataproviders;

use yii\helpers\Json;
use app\models\ClientJson;
use yii\data\BaseDataProvider;

class JsonDataProvider extends BaseDataProvider
{
    /**
     * @var string
     */
    public string $filename;

    /**
     * @var string
     */
    public string $file_content;

    /**
     * @return void
     */
    public function init(): void
    {
        parent::init();
        $this->filename = __DIR__ . '/../data/clients.json';
        $this->file_content = file_get_contents($this->filename);
    }

    /**
     * @return array
     */
    protected function prepareModels(): array
    {
        $models = [];

        if (!empty($this->file_content)) {
            try {
                $models = Json::decode($this->file_content);
            } catch (\Exception $e) {
                return $models;
            }
        }

        return $models;
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareKeys($models): array
    {
        return array_keys($models);
    }

    /**
     * @return int
     */
    protected function prepareTotalCount(): int
    {
        return count($this->models);
    }

    /**
     * @return int
     */
    public static function getLastId(): int
    {
        $models = (new self)->models;
        $last_row = end($models);

        return (int) $last_row['id'];
    }

    /**
     * @param ClientJson $model
     * @return bool
     */
    public function save(ClientJson $model): bool
    {
        $models = $this->models;

        if ($model->isNewRecord) {
            $model->id = self::getLastId() + 1;
            $row = $model->attributes;
            unset($row['isNewRecord']);
            $models[] = $row;
        } else {
            $idx = array_search($model->id, array_column($models, 'id'));
            $row = $model->attributes;
            unset($row['isNewRecord']);
            $models[$idx] = $row;
        }

        if (file_put_contents($this->filename, Json::encode($models))) {
            return true;
        }

        return false;
    }

    /**
     * @param $condition
     * @return ClientJson|null
     */
    public static function findOne($condition): ?ClientJson
    {
        $self = new self;

        $idx = array_search($condition, array_column($self->models, 'id'));
        if ($idx !== false) {
            $model = new ClientJson;
            $model->id = $self->models[$idx]['id'];
            $model->attributes = $self->models[$idx];

            return $model;
        }

        return null;
    }

    /**
     * @param int $id
     * @return bool
     */
    public static function delete(int $id): bool
    {
        $self = new self;
        $models = $self->models;

        $idx = array_search($id, array_column($models, 'id'));

        if ($idx !== false) {
            unset($models[$idx]);
            if (file_put_contents($self->filename, Json::encode($models))) {
                return true;
            }
        }

        return false;
    }
}