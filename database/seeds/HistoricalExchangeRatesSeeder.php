<?php

use Illuminate\Database\Seeder;

class HistoricalExchangeRatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dbModel = app()->make(\App\Models\HistoricalExchangeRate::class);

        $filename = storage_path('exchangeRates.json');
        $dates = json_decode(file_get_contents($filename), true);

        foreach ($dates as $index => $value) {
            if ($value) {
                $date = array_first(array_keys($value), function () {return true;});
                $exchangeRates = array_first(array_values($value), function () {return true;});

                if (!$date || !$exchangeRates) {
                    dump($value, $index);
                }

                if (!$date || !$exchangeRates) {
                    dump($date, $exchangeRates);
                }

                $exchangeRate = $dbModel->newInstance(['date' => $date, 'exchange_rates' => $exchangeRates]);

                $exchangeRate->save();
            }
        }
    }
}
