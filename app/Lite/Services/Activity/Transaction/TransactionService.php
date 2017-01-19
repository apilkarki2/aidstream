<?php namespace App\Lite\Services\Activity\Transaction;

use App\Lite\Services\Data\Traits\TransformsData;
use App\Lite\Services\Traits\ProvidesLoggerContext;
use App\Models\Activity\Transaction;
use Exception;
use Psr\Log\LoggerInterface;
use App\Lite\Contracts\TransactionRepositoryInterface;

/**
 * Class TransactionService
 * @package app\Lite\Services\Transaction
 */
class TransactionService
{
    use ProvidesLoggerContext, TransformsData;

    /**
     * @var TransactionRepositoryInterface
     */
    protected $transactionRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * TransactionService constructor.
     * @param TransactionRepositoryInterface $transactionRepository
     * @param LoggerInterface                $logger
     */
    public function __construct(TransactionRepositoryInterface $transactionRepository, LoggerInterface $logger)
    {
        $this->transactionRepository = $transactionRepository;
        $this->logger                = $logger;
    }

    /**
     * Get all Transactions for the current Organization.
     *
     * @return \Illuminate\Database\Eloquent\Collection|array
     */
    public function all()
    {

    }

    /**
     * Store the Transaction data.
     *
     * @param array $rawData
     * @param       $version
     * @return Transaction|null
     */
    public function store(array $rawData, $version)
    {
        try {
            $activity = $this->transactionRepository->save($this->transform($this->getMapping($rawData, 'Transaction', $version)));

            $this->logger->info('Transaction successfully saved.', $this->getContext());

            return $activity;
        } catch (Exception $exception) {
            $this->logger->error(sprintf('Error due to %s', $exception->getMessage()), $this->getContext($exception));

            return null;
        }
    }

    /**
     *  Find a Specific Transaction.
     *
     * @param $id
     * @return Transaction
     */
    public function find($id)
    {
        return $this->transactionRepository->find($id);
    }

    /**
     * Delete a activity.
     *
     * @param $activityId
     * @return mixed|null
     */
    public function delete($activityId)
    {
        try {
            $activity = $this->transactionRepository->delete($activityId);

            $this->logger->info('Transaction successfully deleted.', $this->getContext());

            return $activity;
        } catch (Exception $exception) {
            $this->logger->error(sprintf('Error due to %s', $exception->getMessage()), $this->getContext($exception));

            return null;
        }
    }

    /**
     * Returns reversely mapped activity data to edit.
     *
     * @param $activityId
     * @param $version
     * @return array
     */
    public function edit($activityId, $version)
    {
        $activity = $this->find($activityId)->toArray();

        return $this->transformReverse($this->getMapping($activity, 'Transaction', $version));
    }

    /**
     * Update the activity data.
     *
     * @param $activityId
     * @param $rawData
     * @param $version
     * @return mixed|null
     */
    public function update($activityId, $rawData, $version)
    {
        try {
            $this->transactionRepository->update($activityId, $this->transform($this->getMapping($rawData, 'Transaction', $version)));
            $this->logger->info('Transaction successfully updated.', $this->getContext());

            return true;
        } catch (Exception $exception) {
            $this->logger->error(sprintf('Error due to %s', $exception->getMessage()), $this->getContext($exception));

            return null;
        }
    }

    /**
     * Returns Budget Model in view format
     * @param $activityId
     * @param $type
     * @param $version
     * @return array
     * @internal param $budget
     */
    public function getModel($activityId, $type, $version)
    {
        $model = json_decode($this->transactionRepository->findByActivityId($activityId), true);

        $newModel = [];

        foreach ($model as $index => $value) {
            if (getVal($value, ['transaction', 'transaction_type', 0, 'transaction_type_code'], '') == $type) {
                $newModel[] = $value;
            }
        }

        $filteredModel = $this->transformReverse($this->getMapping($newModel, 'Transaction', $version));

        return $filteredModel;
    }

    /**
     * Adds new disbursement transaction to the current activity.
     *
     * @param $activityId
     * @param $rawData
     * @param $version
     * @return bool|null
     */
    public function addDisbursement($activityId, $rawData, $version)
    {
        $rawData['type']        = 3;
        $rawData['activity_id'] = $activityId;

        return $this->addTransaction($rawData, $version);
    }

    /**
     * Adds new expenditure transaction to the current activity.
     *
     * @param $activityId
     * @param $rawData
     * @param $version
     * @return bool|null
     */
    public function addExpenditure($activityId, $rawData, $version)
    {
        $rawData['type']        = 4;
        $rawData['activity_id'] = $activityId;

        return $this->addTransaction($rawData, $version);
    }

    /**
     * Adds new disbursement transaction to the current activity.
     *
     * @param $activityId
     * @param $rawData
     * @param $version
     * @return bool|null
     */
    public function addIncomingFunds($activityId, $rawData, $version)
    {
        $rawData['type']        = 1;
        $rawData['activity_id'] = $activityId;

        return $this->addTransaction($rawData, $version);
    }

    /**
     * Deletes a transaction of current activity.
     *
     * @param $request
     * @return bool|null
     */
    public function deleteTransaction($request)
    {
        try {
            $index        = $request->get('index');
            $transactions = $this->find($index);
            $transactions->delete();

            $this->logger->info('Transaction successfully deleted.', $this->getContext());

            return true;
        } catch (Exception $exception) {
            $this->logger->error(sprintf('Error due to %s', $exception->getMessage()), $this->getContext($exception));

            return null;
        }
    }

    public function getFilteredTransactions($transactions)
    {
        $filteredTransactions = [];
        foreach ($transactions as $index => $transaction) {
            $type = getVal($transaction, ['transaction', 'transaction_type', 0, 'transaction_type_code'], '');
            if ($type == 3) {
                $filteredTransactions['disbursement'][] = $transaction;
            }
            if ($type == 4) {
                $filteredTransactions['expenditure'][] = $transaction;
            }
            if ($type == 1) {
                $filteredTransactions['incoming'][] = $transaction;
            }
        }

        return $filteredTransactions;
    }

    public function getDefaultCurrency($activity)
    {
        $settings     = $activity->organization->settings;
        $activityData = json_decode($activity, true);
        $settingsData = json_decode($settings, true);

        $activityCurrency = getVal($activityData, ['default_field_values', 0, 'default_currency'], '');
        if ($activityCurrency) {
            return $activityCurrency;
        }

        $settingsCurrency = getVal($settingsData, ['default_field_values', 0, 'default_currency'], '');
        if ($settingsCurrency) {
            return $settingsCurrency;
        }

        return null;
    }

    protected function addTransaction($rawData, $version)
    {
        try {
            $mappedBudget = $this->transform($this->getMapping($rawData, 'Transaction', $version));

            foreach ($mappedBudget as $index => $value) {
                $this->transactionRepository->save($value);
            }

            $this->logger->info('Transaction successfully added.', $this->getContext());

            return true;
        } catch (Exception $exception) {
            $this->logger->error(sprintf('Error due to %s', $exception->getMessage()), $this->getContext($exception));

            return null;
        }
    }

    public function getTransactionType($type)
    {
        if ($type == 3) {
            return 'Disbursement';
        }

        if ($type == 4) {
            return 'Expenditure';
        }

        if ($type == 1) {
            return 'IncomingFunds';
        }

    }
}
