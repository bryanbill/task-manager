<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Task",
 *     required={"title", "user_id"},
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="title", type="string"),
 *     @OA\Property(property="description", nullable=true, type="string"),
 *     @OA\Property(property="user_id", type="integer"),
 *     @OA\Property(property="due_date", nullable=true, type="string", format="date"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class Task extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'description', 'user_id', 'due_date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}