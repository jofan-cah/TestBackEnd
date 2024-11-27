<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * @OA\Schema(
 *     schema="UserModel",
 *     type="object",
 *     properties={
 *         @OA\Property(property="id", type="integer", example=1, description="Unique identifier of the user"),
 *         @OA\Property(property="name", type="string", example="John Doe", description="Name of the user"),
 *         @OA\Property(property="email", type="string", example="john.doe@example.com", description="Email address of the user"),
 *         @OA\Property(property="phone_number", type="string", example="+628123456789", description="Phone number of the user"),
 *         @OA\Property(property="address", type="string", example="Jl. Example No. 123, Jakarta", description="Address of the user"),
 *         @OA\Property(property="role", type="string", example="super_admin", description="Role of the user (e.g., super_admin, manager, employee)"),
 *         @OA\Property(property="company_id", type="integer", example=1, description="ID of the company the user belongs to"),
 *         @OA\Property(property="created_at", type="string", format="date-time", example="2024-11-27T12:34:56Z", description="Timestamp when the user was created"),
 *         @OA\Property(property="updated_at", type="string", format="date-time", example="2024-11-27T12:34:56Z", description="Timestamp when the user was last updated")
 *     }
 * )
 */



class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    use SoftDeletes;
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'address',
        'role',
        'company_id'
    ];


    protected $hidden = ['password'];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }


    // Relasi: User dapat memiliki banyak karyawan (hanya manager dan super admin)
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    // Role check helper
    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    public function isManager()
    {
        return $this->role === 'manager';
    }

    public function isEmployee()
    {
        return $this->role === 'employee';
    }
}
