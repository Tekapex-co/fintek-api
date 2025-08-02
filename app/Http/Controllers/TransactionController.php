<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientFundsException;
use App\Exceptions\InvalidTransactionException;
use App\Http\Requests\TransferRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Services\TransactionService;
use App\Traits\CustomResponse;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;

class TransactionController extends Controller
{
    use CustomResponse;

    public function __construct(protected readonly TransactionService $transactionService) {}

    public function index()
    {
        try {

            $transactions = $this->transactionService->getTransactions();

            return $this->success(data: TransactionResource::collection($transactions));
        } catch (ModelNotFoundException $exception) {
            Log::critical('Possible hacking attempt: ', [
                'exception' => $exception,
            ]);

            return $this->error(message: $exception->getMessage());
        } catch (Exception $e) {

            return $this->serverError('Error retrieving transactions', $e);
        }
    }

    public function store(TransferRequest $request): JsonResponse
    {
        try {
            $this->transactionService->transfer($request->validated());

            return $this->success(message: 'Transaction initiated successfully.');
        } catch (InsufficientFundsException|InvalidTransactionException $e) {
            return $this->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return $this->serverError('Transaction error', $e);
        }
    }

    /**
     * @throws Exception
     */
    public function show(Transaction $transaction)
    {
        try {
            return $this->success(data: new TransactionResource(
                $this->transactionService->getTransactionDetails($transaction))
            );
        } catch (UnauthorizedException $exception) {
            Log::critical('Possible hacking attempt: ', [
                'exception' => $exception,
            ]);

            return $this->error(message: $exception->getMessage());
        } catch (Exception $e) {

            return $this->serverError('Error retrieving transactions', $e);
        }
    }
}
