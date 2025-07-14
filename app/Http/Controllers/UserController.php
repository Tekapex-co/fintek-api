<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserAccountRequest;
use App\Services\UserService;
use App\Traits\CustomResponse;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    use CustomResponse;

    public function __construct(protected readonly UserService $userService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     * @throws \Exception
     */
    public function store(CreateUserAccountRequest $request): JsonResponse
    {
        try {
            $token = $this->userService->createUserAccount($request->validated());
            return $this->success(
                'User account created successfully',
                ['token' => $token],
                Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            return $this->serverError('Error creating user account', $e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
