<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'company_name' => 'required|unique:companies,name',
            'company_email' => 'required|email|unique:companies,email',
            'company_phone' => 'required|unique:companies,phone_number'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $company = Company::create([
            'name' => $request->company_name,
            'email' => $request->company_email,
            'phone_number' => $request->company_phone
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_id' => $company->id,
            'role' => 'manager'
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'user' => $user,
            'company' => $company,
            'token' => $token
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'token' => $token,
            'user' => JWTAuth::user()
        ]);
    }
}
