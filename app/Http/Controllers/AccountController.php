<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Models\Account;
use App\Services\AccountService;
use App\Traits\CustomResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\UnauthorizedException;

class AccountController extends Controller
{
    use CustomResponse;

    public function __construct(protected readonly AccountService $accountService) {}

    public function index(Account $account)
    {
        try {

            $transactions = $this->accountService->getTransactions($account);

            return $this->success(data: TransactionResource::collection($transactions));
        } catch (UnauthorizedException $exception) {
            Log::critical('Possible hacking attempt: ', [
                'exception' => $exception,
            ]);

            return $this->error(message: $exception->getMessage());
        } catch (\Exception $e) {

            return $this->serverError('Error retrieving transactions', $e);
        }
    }
}
