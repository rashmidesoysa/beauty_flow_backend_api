<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    //
    use SoftDeletes, HasFactory;

    protected $table = 'brands';

    protected $fillable = [
        'category_id',
        'sub_category_id',
        'name',
        'description',
        'image_url',
        'created_by',
        'updated_by',
        'deleted_by'
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

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }
}
