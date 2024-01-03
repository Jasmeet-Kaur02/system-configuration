<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use stdClass;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = customValidate($request->all(), [
            'firstName' => "required|string",
            "lastName" => "required|string",
            "email" => "required|email|unique:users,email",
            "phoneNumber" => "required|string|min:10",
            "gender" => "required|string|in:male,female",
            "role" => "required|string",
            "password" => "required|string|min:8"
        ]);

        $ability = 'server:' . $validatedData['role'];

        $validatedData['password'] = Hash::make($validatedData['password']);

        $user = User::create($validatedData);

        $token = $user->createToken($user->id, [$ability])->plainTextToken;

        $data = new stdClass;
        $data->user = $user;
        $data->token = $token;

        return $this->success($data, "User account has been created successfully", 200);
    }

    public function login(Request $request)
    {
        $validatedData = customValidate($request->all(), [
            'email' => "required|email|exists:users,email",
            "password" => "required|string|min:8"
        ]);

        $user = User::where("email", $validatedData['email'])->first();

        if (!Hash::check($validatedData['password'], $user->password)) {
            return $this->error("Password is incorrect", 400);
        }

        $ability = 'server:' . $user->role;

        $token = $user->createToken($user->id, [$ability])->plainTextToken;

        $data = new stdClass;
        $data->user = $user;
        $data->token = $token;

        return $this->success($data, "User has been logged in successfully", 200);
    }
}
