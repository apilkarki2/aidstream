<?php namespace App\Lite\Contracts;

use App\Models\Activity\Transaction;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface TransactionRepositoryInterface
 * @package App\Lite\Contracts
 */
interface TransactionRepositoryInterface
{
    /**
     * Get all the Transactions of the current Transaction.
     *
     * @param $id
     * @return Collection
     */
    public function all($id);

    /**
     * Find an Transaction by its id.
     *
     * @param $id
     * @return Transaction
     */
    public function find($id);

    /**
     * Save the Transaction data into the database.
     *
     * @param array $data
     * @return mixed
     */
    public function save(array $data);

    /**
     * @param       $id
     * @param array $data
     * @return mixed
     */
    public function update($id, array $data);
}
