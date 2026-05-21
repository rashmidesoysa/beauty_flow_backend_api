<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'items';

    /** @use HasFactory<\Database\Factories\ItemFactory> */

    protected $fillable = [
        'item_code',
        'item_name',
        'description',
        'serial_number',
        'batch_number',
        'barcode',
        'supplier_id',
        'brand_id',
        'category_id',
        'sub_category_id',
        'cost_price',
        'list_price',
        'avg_price',
        'unit_pack',
        'unit_of_measure',
        'group_code',
        'type_code',
        'price_level',
        'status',
        'image_url',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

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
