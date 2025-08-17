<?php

namespace App\Http\Controllers;

use App\Http\Requests\SavingsRequest;
use App\Http\Resources\SavingsResource;
use App\Models\Savings;
use App\Services\SavingsService;
use App\Traits\CustomResponse;
use Symfony\Component\HttpFoundation\Response;

class SavingsController extends Controller
{
    use CustomResponse;

    public function __construct(private readonly SavingsService $savingsService) {}

    public function index()
    {
        try {
            $savingsPlans = $this->savingsService->getSavingsPlans();

            return $this->success(data: SavingsResource::collection($savingsPlans));
        } catch (\Exception $e) {
            return $this->serverError('Error retrieving savings plans', $e);
        }
    }

    public function store(SavingsRequest $request)
    {
        try {
            $newSavingsPlan = $this->savingsService->createSavingsPlan($request->validated());

            return $this->success(
                message: 'Savings account created successfully',
                data: new SavingsResource($newSavingsPlan),
                code: Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            return $this->serverError('Error creating savings account', $e);
        }
    }

    public function show(Savings $savings)
    {
        return new SavingsResource($savings);
    }

    public function update(SavingsRequest $request, Savings $savings)
    {
        $savings->update($request->validated());

        return new SavingsResource($savings);
    }

    public function destroy(Savings $savings)
    {
        $savings->delete();

        return response()->json();
    }
}
