<?php namespace App\Lite\Repositories\Activity\Transaction;

use App\Lite\Contracts\TransactionRepositoryInterface;
use App\Models\Activity\Transaction;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class TransactionRepository
 * @package App\Lite\Repositories\Transaction
 */
class TransactionRepository implements TransactionRepositoryInterface
{
    /**
     * @var Transaction
     */
    protected $transaction;

    /**
     * TransactionRepository constructor.
     *
     * @param Transaction $transaction
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Get all the Transactions of the current Transaction.
     *
     * @param $id
     * @return Collection
     */
    public function all($id)
    {
        // TODO: Implement all() method.
    }

    /**
     * Find an Transaction by its id.
     *
     * @param $id
     * @return Transaction
     */
    public function find($id)
    {
        return $this->transaction->findOrFail($id);
    }

    /**
     * Find Transactions by activity id
     *
     * @param $id
     * @return mixed
     */
    public function findByActivityId($id)
    {
        return $this->transaction->where('activity_id', $id)->get();
    }

    /**
     * Save the Transaction data into the database.
     *
     * @param array $data
     * @return mixed
     */
    public function save(array $data)
    {
        return $this->transaction->create($data);
    }

    /**
     * Update current transactions
     *
     * @param array $data
     * @return mixed
     * @internal param $id
     */
    public function update(array $data)
    {
        $transaction = $this->transaction->findOrFail($data['id']);

        return $transaction->update($data);
    }
}
