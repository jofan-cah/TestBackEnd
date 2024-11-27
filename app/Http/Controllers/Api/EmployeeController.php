<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;


/**
 * @OA\Tag(
 *     name="Employee",
 *     description="Operations related to employees"
 * )
 */

class EmployeeController extends Controller implements HasMiddleware
{
    //
    public static function middleware(): array
    {
        return [
            // Middleware untuk semua metode
            'jwt.auth',

            // Middleware spesifik untuk metode tertentu
            new Middleware('role:employee', only: ['getFellowEmployees', 'showFellowEmployee'])
        ];
    }



    /**
     * @OA\Get(
     *     path="/api/employees/{id}",
     *     summary="Get employee details",
     *     tags={"Employee"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Employee ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Employee details",
     *         @OA\JsonContent(ref="#/components/schemas/Employee")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     security={{"jwt.auth": {}}},
     * )
     */

    public function show($id)
    {
        // Ambil detail employee berdasarkan ID
        $employee = Employee::with('user')->findOrFail($id);

        // Verifikasi apakah employee tersebut berada dalam perusahaan yang sama dengan user yang login
        if ($employee->company_id !== auth()->user()->company_id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json($employee);
    }


    /**
     * @OA\Get(
     *     path="/api/employees",
     *     summary="Get all employees in the same company",
     *     tags={"Employee"},
     *     @OA\Response(
     *         response=200,
     *         description="List of employees in the same company",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Employee")
     *         )
     *     ),
     *     security={{"jwt.auth": {}}},
     * )
     */

    // Melihat semua employee dalam perusahaan yang sama
    public function index(Request $request)
    {
        // Ambil daftar employee berdasarkan perusahaan yang sama dengan user yang sedang login
        $employees = Employee::where('company_id', auth()->user()->company_id)
            ->get();

        return response()->json($employees);
    }


    // Menambah employee baru

    /**
     * @OA\Post(
     *     path="/api/employees",
     *     summary="Create a new employee",
     *     tags={"Employee"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Employee")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Employee created",
     *         @OA\JsonContent(ref="#/components/schemas/Employee")
     *     ),
     *     security={{"jwt.auth": {}}},
     * )
     */
    public function store(Request $request)
    {
        // Validasi data input
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'address' => 'nullable|string',
            'company_id' => 'required|exists:companies,id',
        ]);

        // Buat employee baru
        $employee = Employee::create($validatedData);

        return response()->json($employee, 201);
    }

    // Mengupdate data 

    /**
     * @OA\Put(
     *     path="/api/employees/{id}",
     *     summary="Update employee details",
     *     tags={"Employee"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Employee ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Employee")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Employee updated",
     *         @OA\JsonContent(ref="#/components/schemas/Employee")
     *     ),
     *     security={{"jwt.auth": {}}},
     * )
     */
    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        // Validasi data
        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'phone_number' => 'sometimes|required|string|max:20',
            'address' => 'nullable|string',
        ]);

        $employee->update($validatedData);

        return response()->json($employee);
    }

    // Menghapus employee

    /**
     * @OA\Delete(
     *     path="/api/employees/{id}",
     *     summary="Delete an employee",
     *     tags={"Employee"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Employee ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Employee deleted"
     *     ),
     *     security={{"jwt.auth": {}}},
     * )
     */
    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();

        return response()->json(null, 204);
    }

    // To get employees in the same company

    /**
     * @OA\Get(
     *     path="/api/employees/fellow",
     *     summary="Get fellow employees in the same company",
     *     tags={"Employee"},
     *     @OA\Response(
     *         response=200,
     *         description="List of fellow employees",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Employee")
     *         )
     *     ),
     *     security={{"jwt.auth": {}}},
     * )
     */
    public function getFellowEmployees(Request $request)
    {
        $user = auth()->user();

        // Ambil semua employee dalam perusahaan yang sama kecuali user yang sedang login
        $employees = User::where('role', 'employee')
            ->where('company_id', $user->company_id)
            ->where('id', '!=', $user->id)
            ->get();

        return response()->json($employees, 200);
    }


    // Show detail of a fellow employee in the same company
    public function showFellowEmployee($id)
    {
        $user = auth()->user();

        // Ambil detail employee dalam perusahaan yang sama
        $employee = User::where('role', 'employee')
            ->where('id', $id)
            ->where('company_id', $user->company_id)
            ->first();

        // Jika tidak ditemukan, berikan respons Unauthorized
        if (!$employee) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json($employee, 200);
    }
}
