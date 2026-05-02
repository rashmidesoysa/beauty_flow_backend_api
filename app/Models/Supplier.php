<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    /** @use HasFactory<\Database\Factories\SupplierFactory> */
   protected $table = '_tblm__customer';

    protected $fillable = [
        'sup_code',
        'fname',
        'lname',
        'email',
        'phone',
        'address',
        'city',
        'tax_code',
        'current_balance',
        'is_active',
        'remarks',
        'created_by',
        'updated_by',
        'deleted_by'

    ];
    protected $casts = [
        'is_active' => 'boolean',
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
