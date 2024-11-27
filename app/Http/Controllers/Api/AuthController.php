<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     properties={
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="John Doe"),
 *         @OA\Property(property="email", type="string", example="john.doe@example.com"),
 *         @OA\Property(property="phone_number", type="string", example="+628123456789"),
 *         @OA\Property(property="address", type="string", example="Jl. Example No. 123"),
 *         @OA\Property(property="role", type="string", example="super_admin"),
 *         @OA\Property(property="created_at", type="string", format="date-time"),
 *         @OA\Property(property="updated_at", type="string", format="date-time")
 *     }
 * )
 */


class AuthController extends Controller
{
    //

    /**
     * @OA\Post(
     *      path="/auth/register",
     *      operationId="registerUser",
     *      tags={"Authentication"},
     *      summary="Register user baru",
     *      description="Mendaftarkan user baru ke dalam sistem",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *              @OA\Property(property="password", type="string", example="password123"),
     *              @OA\Property(property="phone_number", type="string", example="+628123456789"),
     *              @OA\Property(property="address", type="string", example="Jl. Example No. 123")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="User successfully registered",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="User created successfully"),
     *              @OA\Property(property="user", type="object", ref="#/components/schemas/User")
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="The given data was invalid.")
     *          )
     *      )
     * )
     */
    public function register(Request $request)
    {
        // Validasi data yang diterima
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);



        // Membuat user baru
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => 'super_admin', // Role default
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ], 201);
    }

    /**
     * @OA\Post(
     *      path="/auth/login",
     *      operationId="loginUser",
     *      tags={"Authentication"},
     *      summary="Login user",
     *      description="Endpoint untuk login user",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *              @OA\Property(property="password", type="string", example="password123")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Login berhasil",
     *          @OA\JsonContent(
     *              @OA\Property(property="token", type="string", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Invalid credentials",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Unauthorized")
     *          )
     *      )
     * )
     */
    public function login(Request $request)
    {
        // Validasi email dan password, memastikan keduanya tidak kosong
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6', // Pastikan password tidak kosong
        ]);

        $credentials = $request->only('email', 'password');

        // Jika data valid dan autentikasi gagal
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json(['token' => $token]);
    }
}
