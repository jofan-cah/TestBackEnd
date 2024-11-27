<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends Controller implements HasMiddleware
{
    //



    public static function middleware(): array
    {
        return [
            // Middleware untuk semua metode
            'jwt.auth',

            // Middleware spesifik untuk metode tertentu
            new Middleware('role:manager', only: ['updateOwnInfo']),
            new Middleware('role:employee', only: ['getFellowEmployees', 'showFellowEmployee'])
        ];
    }

    // Menampilkan daftar semua manager

    /**
     * @OA\Get(
     *     path="/api/users/managers",
     *     tags={"User"},
     *     summary="Get a list of managers",
     *     description="Retrieve a list of managers from the user's company",
     *     @OA\Response(
     *         response=200,
     *         description="A list of managers",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/UserModel"))
     *     )
     * )
     */
    public function getManagers(Request $request)
    {
        // Hanya menampilkan manager yang ada di perusahaan pengguna saat ini
        $managers = User::where('role', 'manager')
            ->where('company_id', auth()->user()->company_id)
            ->get();

        return response()->json($managers);
    }

    // Melihat daftar semua karyawan
    /**
     * @OA\Get(
     *     path="/api/users/employees",
     *     tags={"User"},
     *     summary="Get a list of employees",
     *     description="Retrieve a list of employees from the user's company",
     *     @OA\Response(
     *         response=200,
     *         description="A list of employees",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Employee"))
     *     )
     * )
     */
    public function getEmployees(Request $request)
    {
        // Hanya menampilkan karyawan yang ada di perusahaan pengguna saat ini
        $employees = Employee::where('company_id', auth()->user()->company_id)
            ->get();

        return response()->json($employees);
    }

    // Melihat detail informasi user (manager atau employee)

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     tags={"User"},
     *     summary="Get user details",
     *     description="Get details of a specific user by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User details",
     *         @OA\JsonContent(ref="#/components/schemas/UserModel")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function show($id)
    {
        // Ambil user berdasarkan id
        $user = User::findOrFail($id);

        // Verifikasi apakah user yang mengakses adalah user yang diminta atau manager
        if ($user->id !== auth()->id() && auth()->user()->role !== 'manager') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json($user);
    }

    // Mengupdate informasi diri manager (nama, email, dll.)
    /**
     * @OA\Put(
     *     path="/api/users/updateOwnInfo",
     *     tags={"User"},
     *     summary="Update own user information",
     *     description="Authenticated users can update their own information (name, email, phone number, etc.). Only the user themselves can update their data.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="John Doe", description="Updated name of the user"),
     *             @OA\Property(property="email", type="string", example="john.doe@example.com", description="Updated email of the user"),
     *             @OA\Property(property="phone_number", type="string", example="+628123456789", description="Updated phone number of the user"),
     *             @OA\Property(property="address", type="string", example="Jl. Example No. 123, Jakarta", description="Updated address of the user")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User information updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/UserModel")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input data"
     *     )
     * )
     */
    public function updateOwnInfo(Request $request, $id)
    {
        // Verifikasi bahwa user yang diminta adalah user yang login
        if ($id != auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Validasi data yang diterima
        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'phone_number' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        // Update informasi manager
        $user = User::findOrFail($id);
        $user->update($validatedData);

        return response()->json($user);
    }

    // Menambah user baru (super_admin only)


    /**
     * @OA\Post(
     *     path="/api/users",
     *     tags={"User"},
     *     summary="Create a new user",
     *     description="Only super_admin can create a new user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="role", type="string", enum={"super_admin", "manager", "employee"}),
     *             @OA\Property(property="company_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created",
     *         @OA\JsonContent(ref="#/components/schemas/UserModel")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     * )
     */
    public function store(Request $request)
    {
        // Validasi data input
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:super_admin,manager,employee',
            'company_id' => 'required|exists:companies,id',
        ]);

        // Buat user baru
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => $validatedData['role'],
            'company_id' => $validatedData['company_id'],
        ]);

        return response()->json($user, 201);
    }


    // Mengupdate informasi user (super_admin or manager can update)
    /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     tags={"User"},
     *     summary="Update user information",
     *     description="Update user information (only super admin or manager can do this)",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="role", type="string", enum={"super_admin", "manager", "employee"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated user details",
     *         @OA\JsonContent(ref="#/components/schemas/UserModel")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        // Validasi data yang diterima
        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'role' => 'sometimes|required|in:super_admin,manager,employee',
            'company_id' => 'sometimes|required|exists:companies,id',
        ]);

        // Update user berdasarkan ID
        $user = User::findOrFail($id);
        $user->update($validatedData);

        return response()->json($user);
    }

    // Menghapus user (super_admin only)

    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     tags={"User"},
     *     summary="Delete a user",
     *     description="Only super_admin can delete a user",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="User deleted"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Hanya super_admin yang bisa menghapus user
        if (auth()->user()->role !== 'super_admin') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted'], 204);
    }
}
