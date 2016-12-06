<?php namespace App\Console\Commands;

use App\Models\Activity\Transaction;
use Illuminate\Console\Command;

class TransactionDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dates {--getDate} {--getRate}';

    protected $transaction;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Transaction $transaction)
    {
        parent::__construct();

        $this->transaction = $transaction;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->option('getDate')) {
            if (method_exists($this, 'getDates')) {
                $this->getDates();
            }
        }

        if ($this->option('getRate')) {
            if (method_exists($this, 'getRates')) {
                $this->getRates();
            }
        }
    }

    protected function getDates()
    {
        $dates = [];

        foreach ($this->transaction->all() as $transaction) {
            $date = getVal($transaction->transaction['transaction_date'], [0, 'date'], null);

            if ($date) {
                $date    = date('Y-m-d', strtotime($date));
                $dates[] = $date;
            }
        }

        $dates = array_unique($dates);

        file_put_contents(storage_path('dates.json'), json_encode($dates));

        $this->info('Done');
    }

    protected function getRates()
    {
        $accessKeySbs = "f20cf644d131e00e2918d42dd8cbcd1f";
        $accessKey1   = "b3251f36cea97bf477c77b7fbf91d19d";
        $accessKey2   = "8095e751246688838fa6de6d12036bea";

        $dates        = json_decode(file_get_contents(storage_path('dates.json')), true);

        $first  = array_slice($dates, 0, 1000);
        $second = array_slice($dates, 1001);

        $firstPart  = $this->getExchangeRatesFor($first, $accessKey2);
        $secondPart = $this->getExchangeRatesFor($second, $accessKey1);

        $dates = array_merge($firstPart, $secondPart);
        file_put_contents(storage_path('exchangeRates.json'), json_encode($dates));

        dd(count(json_decode(file_get_contents(storage_path('exchangeRates.json')), true)), json_decode(file_get_contents(storage_path('exchangeRates.json')), true));
    }

    protected function getExchangeRatesFor(array $dates, $accessKey)
    {
        $exchangeRates = [];

        foreach ($dates as $date) {
            $json            = $this->curl($date, $accessKey);
            $exchangeRates[] = $this->clean(json_decode($json, true), $date);
        }

        return $exchangeRates;
    }

    protected function curl($date, $accessKey)
    {
        $ch = curl_init('http://apilayer.net/api/historical' . '?access_key=' . $accessKey . '&date=' . $date . '&format=1');

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }


    protected function clean($json, $date)
    {
        $rates = [];

        if (!$json) {
            $json = (array) $json;
        }

        foreach (getVal($json, ['quotes'], []) as $key => $value) {
            $toCurrency = str_replace('USD', '', $key);

            if ($toCurrency !== '') {
                $rates[$date][$toCurrency] = $value;
            }
        }

        return $rates;
    }
}
