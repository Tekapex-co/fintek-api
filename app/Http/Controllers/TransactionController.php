<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientFundsException;
use App\Exceptions\InvalidTransactionException;
use App\Http\Requests\TransferRequest;
use App\Services\TransactionService;
use App\Traits\CustomResponse;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TransactionController extends Controller
{
    use CustomResponse;

    public function __construct(protected readonly TransactionService $transactionService) {}

    public function transfer(TransferRequest $request): JsonResponse
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
}
