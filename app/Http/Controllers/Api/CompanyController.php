<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Hash;

class CompanyController extends Controller implements HasMiddleware
{
    // Konstruktor untuk middleware
    public static function middleware(): array
    {
        return [
            // Middleware untuk semua metode
            'jwt.auth',

            // Middleware spesifik untuk metode tertentu
            new Middleware('role:super_admin', only: ['store', 'update', 'destroy'])
        ];
    }

    /**
     * @OA\Get(
     *     path="/api/companies",
     *     summary="Get all companies",
     *     tags={"Company"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search for companies by name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Sort companies by a field",
     *         required=false,
     *         @OA\Schema(type="string", default="created_at")
     *     ),
     *     @OA\Parameter(
     *         name="sort_direction",
     *         in="query",
     *         description="Sort direction (asc/desc)",
     *         required=false,
     *         @OA\Schema(type="string", default="desc")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of companies",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Company")),
     *             @OA\Property(property="meta", type="object", description="Pagination metadata")
     *         )
     *     )
     * )
     */

    // Menampilkan daftar perusahaan dengan pagination, sorting, dan pencarian
    public function index(Request $request)
    {
        $query = Company::query();

        // Pencarian berdasarkan nama
        if ($request->has('search')) {
            $query->where('name', 'LIKE', '%' . $request->search . '%');
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // Pagination
        $companies = $query->paginate(2);

        return response()->json($companies);
    }

    // Membuat perusahaan baru
    /**
     * @OA\Post(
     *     path="/api/companies",
     *     summary="Create a new company",
     *     tags={"Company"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Company")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Company created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="company", ref="#/components/schemas/Company"),
     *             @OA\Property(property="manager_account", type="object", 
     *                 @OA\Property(property="email", type="string", example="manager@company.com"),
     *                 @OA\Property(property="default_password", type="string", example="password123")
     *             )
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:companies',
            'email' => 'required|email|max:255|unique:companies',
            'phone_number' => 'required|string|max:20',
        ]);

        // Buat perusahaan baru
        $company = Company::create($validatedData);

        // Buat akun manager untuk perusahaan
        $manager = User::create([
            'name' => $company->name . ' Manager',
            'email' => $company->email,
            'password' => Hash::make('password123'), // Password default
            'role' => 'manager',
            'company_id' => $company->id,
        ]);

        return response()->json([
            'company' => $company,
            'manager_account' => [
                'email' => $manager->email,
                'default_password' => 'password123',
            ],
        ], 201);
    }

    // Menampilkan detail perusahaan
    /**
     * @OA\Get(
     *     path="/api/companies/{id}",
     *     summary="Get company details",
     *     tags={"Company"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the company",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Company details",
     *         @OA\JsonContent(ref="#/components/schemas/Company")
     *     )
     * )
     */
    public function show($id)
    {
        $company = Company::withTrashed()->findOrFail($id);
        return response()->json($company);
    }

    // Memperbarui data perusahaan
    /**
     * @OA\Put(
     *     path="/api/companies/{id}",
     *     summary="Update company details",
     *     tags={"Company"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the company",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Company")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated company details",
     *         @OA\JsonContent(ref="#/components/schemas/Company")
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);

        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|unique:companies,name,' . $id,
            'email' => 'sometimes|required|email|unique:companies,email,' . $id,
            'phone_number' => 'sometimes|required|unique:companies,phone_number,' . $id
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 400);
        }

        // Update perusahaan
        $company->update($request->only([
            'name',
            'email',
            'phone_number'
        ]));

        return response()->json($company);
    }

    // Menghapus perusahaan (soft delete)

    /**
     * @OA\Delete(
     *     path="/api/companies/{id}",
     *     summary="Delete a company",
     *     tags={"Company"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the company",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Company deleted successfully"
     *     )
     * )
     */
    public function destroy($id)
    {
        $company = Company::findOrFail($id);
        $company->delete();

        return response()->json(null, 204);
    }
}
