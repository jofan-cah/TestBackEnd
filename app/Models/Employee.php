<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Employee",
 *     type="object",
 *     title="Employee",
 *     description="Employee Model",
 *     required={"name", "phone_number", "company_id"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="phone_number", type="string", example="1234567890"),
 *     @OA\Property(property="address", type="string", example="123 Street, City"),
 *     @OA\Property(property="company_id", type="integer", example=1),
 *     @OA\Property(property="position", type="string", example="Software Engineer"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    // Tentukan nama tabel (jika tidak menggunakan penamaan default)
    protected $table = 'employees';

    // Tentukan kolom yang dapat diisi
    protected $fillable = [
        'name',
        'phone_number',
        'address',
        'company_id',
        'position',  // Menambahkan kolom posisi (misalnya jabatan)
    ];

    // Relasi ke User (one-to-one) jika diperlukan
    public function user()
    {
        return $this->belongsTo(User::class);  // Employee belongs to User
    }

    // Tentukan relasi dengan Company
    public function company()
    {
        return $this->belongsTo(Company::class); // Relasi dengan model Company
    }

    // Tentukan relasi dengan Manager jika diperlukan
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id'); // Jika ada relasi dengan manager
    }
}
