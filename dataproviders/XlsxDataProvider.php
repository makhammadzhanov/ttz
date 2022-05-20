<?php

namespace app\dataproviders;

use app\models\ClientXlsx;
use yii\data\BaseDataProvider;
use PhpOffice\PhpSpreadsheet\{IOFactory, Spreadsheet};
use yii\helpers\Json;

class XlsxDataProvider extends BaseDataProvider
{
    /**
     * @var string
     */
    public string $filename;

    /**
     * @var Spreadsheet|null
     */
    public ?Spreadsheet $spreadsheet;

    /**
     * @var array
     */
    public array $fields = [
        'id', 'fullname', 'email', 'phone_number'
    ];

    /**
     * @return void
     */
    public function init(): void
    {
        parent::init();
        $this->filename = __DIR__ . '/../data/clients.xlsx';
        try {
            $this->spreadsheet = IOFactory::load($this->filename);
        } catch (\Exception $e) {
            $this->spreadsheet = null;
        }
    }

    /**
     * @return array
     */
    protected function prepareModels(): array
    {
        $models = [];

        if ($this->spreadsheet !== null) {
            $sheet = $this->spreadsheet->getActiveSheet();
            $data = $sheet->toArray();

            $headers = $data[0];

            if ($headers !== $this->fields) {
                return $models;
            }

            unset($data[0]);

            foreach ($data as $row) {
                if (count($row) === count($this->fields)) {
                    $models[] = array_combine($headers, $row);
                }
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
     * @param ClientXlsx $model
     * @return bool
     */
    public function save(ClientXlsx $model): bool
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

        array_unshift($models, $this->fields);

        $worksheet = $this->spreadsheet->getActiveSheet();
        $worksheet->fromArray($models);

        try {
            $writer = IOFactory::createWriter($this->spreadsheet, 'Xlsx');
            $writer->save($this->filename);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param $condition
     * @return ClientXlsx|null
     */
    public static function findOne($condition): ?ClientXlsx
    {
        $self = new self;

        $idx = array_search($condition, array_column($self->models, 'id'));
        if ($idx !== false) {
            $model = new ClientXlsx;
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

            array_unshift($models, $self->fields);

            $sheetIndex = $self->spreadsheet->getIndex(
                $self->spreadsheet->getActiveSheet()
            );
            $self->spreadsheet->removeSheetByIndex($sheetIndex);
            $self->spreadsheet->createSheet();

            $worksheet = $self->spreadsheet->getActiveSheet();
            $worksheet->fromArray($models);

            try {
                $writer = IOFactory::createWriter($self->spreadsheet, 'Xlsx');
                $writer->save($self->filename);

                return true;
            } catch (\Exception $e) {
                return false;
            }

        }

        return false;
    }
}