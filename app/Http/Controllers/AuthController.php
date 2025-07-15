<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use App\Traits\CustomResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    use CustomResponse;

    public function __construct(protected readonly AuthService $authService)
    {
    }

    public function store(LoginRequest $request): JsonResponse
    {
        try {

            $response = $this->authService->login($request->validated());

            return $this->success('Login successful', $response);
        } catch (ValidationException $e) {

            return $this->error(message: $e->getMessage());
        } catch (\Exception $e) {

            return $this->serverError('Error logging in', $e);
        }
    }

    public function destroy()
    {
        try {

            $user = Auth::user();

            $user->tokens()->delete();

            return $this->success('Logout successful', code: Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return $this->serverError('Error logging out', $e);
        }
    }
}
