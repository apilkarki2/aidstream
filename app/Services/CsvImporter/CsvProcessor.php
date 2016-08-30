<?php namespace App\Services\CsvImporter;

use App\Services\CsvImporter\Entities\Activity\Activity;
use App\Services\CsvImporter\Entities\Activity\Components\ActivityRow;

class CsvProcessor
{
    protected $csv;

    protected $data;

    public $model;

    /**
     * @var array
     */
    protected $activityContainer = [];

    protected $csvIdentifier = 'activity_identifier';

    public function __construct($csv)
    {
        $this->csv = $csv;
    }

    public function handle()
    {
        $this->make('App\Services\CsvImporter\Entities\Activity\Activity');
        $this->groupValues($this->csv);
        $this->model->process()
                    ->validate()
                    ->keep();

    }

    protected function make($class)
    {
        if (class_exists($class)) {
            $this->model = app()->make($class, [$this->data]);
        }
    }

    /**
     * @param $csv
     */
    protected function groupValues($csv)
    {
        $index = - 1;
        foreach ($csv as $row) {
            $sameElement = $this->isSameElement($row);
            if (!$sameElement) {
                $index ++;
                foreach ($row as $key => $value) {
                    $this->setValue($index, $key, $value);
                }
            } else {
                foreach ($row as $key => $value) {
                    $this->setValue($index, $key, $value);
                }
            }
        }
        dd($this->activityContainer);
    }

    protected function setValue($index, $key, $value)
    {
        if (!isset($this->activityContainer[$index][$key])) {
            $this->activityContainer[$index][$key] = null;
        }

        if (!(is_null($value) || $value == "")) {
            $this->activityContainer[$index][$key][] = $value;
        }
    }

    /**
     * @param $row
     * @return bool
     */
    protected function isSameElement($row)
    {
        if (is_null($row[$this->csvIdentifier]) || $row[$this->csvIdentifier] == '') {
            return true;
        }

        return false;
    }

}
