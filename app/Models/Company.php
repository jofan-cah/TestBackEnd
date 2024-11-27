<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Company",
 *     type="object",
 *     title="Company",
 *     description="Company Model",
 *     required={"name", "email", "phone_number"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Example Company"),
 *     @OA\Property(property="email", type="string", example="example@company.com"),
 *     @OA\Property(property="phone_number", type="string", example="1234567890"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */

class Company extends Model
{
    //
    use SoftDeletes;
    use HasFactory;




    protected $fillable = ['name', 'email', 'phone_number'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
