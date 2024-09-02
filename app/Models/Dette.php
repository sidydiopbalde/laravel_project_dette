<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Dette",
 *     type="object",
 *     title="Dette",
 *     required={"client_id", "montant", "montant_due", "date"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="client_id", type="integer", example=1),
 *     @OA\Property(property="montant", type="number", format="float", example=150.75),
 *     @OA\Property(property="montant_due", type="number", format="float", example=150.75),
 *     @OA\Property(property="date", type="string", format="date", example="2023-09-01"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-09-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-09-01T00:00:00Z")
 * )
 */

class Dette extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'montant','montant_due', 'description', 'date'];
    protected $hidden=['id','created_at','updated_at'];
    public function client()
    {
        return $this->belongsTo(Clients::class);

    }

    
}
