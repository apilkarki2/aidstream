<?php namespace App\Console\Commands;

use App\Models\Activity\Activity;
use App\Models\Activity\Transaction;
use Illuminate\Console\Command;

class TransactionDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dates {--getDate} {--getRate} {--getBudget}';

    protected $transaction;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * @var Activity
     */
    protected $activity;

    /**
     * Create a new command instance.
     *
     * @param Transaction $transaction
     * @param Activity    $activity
     */
    public function __construct(Transaction $transaction, Activity $activity)
    {
        parent::__construct();

        $this->transaction = $transaction;
        $this->activity    = $activity;
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

        if ($this->option('getBudget')) {
            if (method_exists($this, 'getBudgets')) {
                $this->getBudgets();
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

        $dates = json_decode(file_get_contents(storage_path('dates.json')), true);

        $first  = array_slice($dates, 0, 1000);
        $second = array_slice($dates, 1001);

        $firstPart  = $this->getExchangeRatesFor($first, $accessKey2);
        $secondPart = $this->getExchangeRatesFor($second, $accessKey1);

        $dates = array_merge($firstPart, $secondPart);
        file_put_contents(storage_path('exchangeRates.json'), json_encode($dates));

        dd(count(json_decode(file_get_contents(storage_path('exchangeRates.json')), true)), json_decode(file_get_contents(storage_path('exchangeRates.json')), true));
    }

    protected function getBudgets()
    {
        $budgetValueDates = [];
        $accessKey        = 'c92a72092ee24a60fc0e0cb7fd1377bf';

        foreach ($this->activity->all() as $activity) {
            if ($activity->budget) {
                $date = getVal($activity->budget, [0, 'value', 0, 'value_date'], null);

                if ($date) {
                    $budgetValueDates[] = $date;
                }
            }
        }

        $budgetValueDates = array_unique($budgetValueDates);
        $a                = json_decode(file_get_contents(storage_path('exchangeRates.json')), true);
        $presentDates     = [];

        foreach ($a as $v) {
            if ($v) {
                $presentDates[] = array_values(array_keys($v))[0];
            }
        }

        $budgetExchangeRates = $this->getExchangeRatesFor($budgetValueDates, $accessKey);

        file_put_contents(storage_path('budgetExchangeRates.json'), json_encode($budgetExchangeRates));

        dd(count($budgetExchangeRates));
    }

    protected function getExchangeRatesFor(array $dates, $accessKey, $presentDates = null)
    {
        $exchangeRates = [];

        if (!$presentDates) {
            foreach ($dates as $date) {
                $json            = $this->curl($date, $accessKey);
                $exchangeRates[] = $this->clean(json_decode($json, true), $date);
            }

            return $exchangeRates;
        }

        foreach ($dates as $date) {
            if (!in_array($date, $presentDates)) {
                $json            = $this->curl($date, $accessKey);
                $exchangeRates[] = $this->clean(json_decode($json, true), $date);
            }
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
