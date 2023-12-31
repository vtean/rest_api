<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use HttpResponses;

    public function login(LoginUserRequest $request)
    {
        $request->validated($request->all());

        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return $this->error('', 'Invalid credentials.', 401);
        }

        $user = User::where('email', $request->email)->first();

        $userToken = $user->createToken('auth token for ' . $user->name)->plainTextToken;

        return $this->success([
            'user' => $user,
            'token' => 
        ]);
    }

    public function register(StoreUserRequest $request)
    {
        $request->validated($request->all());

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make(
                $request->password
            ),
            'google_id' => null
        ]);

        return $this->success([
            'user' => $user,
            'token' => $user->createToken('auth token for ' . $user->name)->plainTextToken
        ]);
    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();

        return $this->success([
            'message' => 'Logged out successfully.'
        ]);
    }
}
