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
     * @param $version
     * @return array
     * @internal param $budget
     */
    public function getModel($activityId, $version)
    {
        $model = json_decode($this->transactionRepository->find($activityId), true);

        $filteredModel = $this->transformReverse($this->getMapping($model, 'Budget', $version));

        return $filteredModel;
    }

    /**
     * Adds new budgets to the current activity.
     *
     * @param $activityId
     * @param $rawData
     * @param $version
     * @return bool|null
     */
    public function addDisbursement($activityId, $rawData, $version)
    {
        $rawData['type'] = 3;
        $rawData['activity_id'] = $activityId;

        try {
            $mappedBudget = $this->transform($this->getMapping($rawData, 'Transaction', $version));

            $this->transactionRepository->save($mappedBudget);

            $this->logger->info('Transaction successfully added.', $this->getContext());

            return true;
        } catch (Exception $exception) {
            $this->logger->error(sprintf('Error due to %s', $exception->getMessage()), $this->getContext($exception));

            return null;
        }
    }

    /**
     * Deletes a budget from current activity.
     *
     * @param $activityId
     * @param $request
     * @return bool|null
     */
    public function deleteBudget($activityId, $request)
    {
        try {
            $activity = $this->find($activityId);
            $budget   = $activity->budget;

            unset($budget[$request->get('index')]);

            $activity->budget = array_values($budget);

            $activity->save();

            $this->logger->info('Budget transaction successfully deleted.', $this->getContext());

            return true;
        } catch (Exception $exception) {
            $this->logger->error(sprintf('Error due to %s', $exception->getMessage()), $this->getContext($exception));

            return null;
        }
    }
}
