<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginUserRequest;
use App\Models\User;
use App\Permissions\V1\Abilities;
use App\Traits\ApiReponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiReponses;

    /**
     * Login
     *
     * Authenticate user and return the user's API token
     * @unauthenticated
     * @group Authentication
     * @response 200 {
     * "message": "Authenticated",
     * "data": {
     * "token": "{YOUR_AUTH_KEY}"
     * },
     * "status": 200
     */

    public function login(LoginUserRequest $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->error('Invalid credentials', 401);
        }

        $user = User::firstWhere('email', $request->email);

        return $this->ok(
            'Authenticated',
            [
                'token' => $user->createToken(
                    'API token for ' . $user->email,
                    Abilities::getAbilities($user),
                    now()->addDays(7)
                )->plainTextToken,
            ]
        );
    }

    /**
     * Loout
     *
     * Signs out the user and destroys the API token
     * @group Authentication
     * @response 200 {}
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->ok('');
    }
}
