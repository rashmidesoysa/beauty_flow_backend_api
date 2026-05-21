<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes, HasFactory;
    /** @use HasFactory<\Database\Factories\SupplierFactory> */
    protected $table = 'supplier';


    protected $fillable = [
        'sup_code',
        'fname',
        'lname',
        'email',
        'phone',
        'address',
        'city',
        'Taxcode',
        'currentBalance',
        'remarks',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'currentBalance' => 'decimal:2',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
