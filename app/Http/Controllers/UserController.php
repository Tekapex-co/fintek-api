<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserAccountRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use App\Traits\CustomResponse;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    use CustomResponse;

    public function __construct(protected readonly UserService $userService) {}

    /**
     * Store a newly created resource in storage.
     *
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
    public function show(): JsonResponse
    {
        try {
            $user = Auth()->user();

            if (! $user) {
                return $this->error('User not found');
            }

            return $this->success(
                'User account retrieved successfully',
                new UserResource($user->load('account'))
            );
        } catch (\Exception $e) {
            return $this->serverError('Error retrieving user account', $e);
        }
    }
}
