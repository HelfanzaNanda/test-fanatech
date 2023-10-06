<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $email = $request->email;
        $password = $request->password;
        $credentials = [
            'email' => $email,
            'password' => $password,
        ];

        try {
            if (!Auth::attempt($credentials)) {
                return response()->json(ResponseHelper::warning(message: "Unauthorized"), 401);
            }



            $user = auth()->user();
            $data = [
                'user' => $user,
                'role' => $user->getRoleNames()[0],
                // 'access_token' => $token,
                // 'token_type' => 'bearer',
                // 'expires_in' => auth()->factory()->getTTL() * 60
            ];
            return response()->json(ResponseHelper::success(data: $data), 200);
        } catch (\Throwable $th) {
            return response()->json(ResponseHelper::error(th: $th), 500);
        }

    }

    public function logout()
    {
        auth()->logout();
        return response()->json(ResponseHelper::success(), 200);

    }

}
