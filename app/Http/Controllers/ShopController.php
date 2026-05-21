<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    public function getItems(Request $request)
    {
        $query = Item::with(['category', 'brand', 'supplier'])
            ->where('status', true)
            ->whereNull('deleted_at');

        // Search by name or description
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category') && $request->category != '' && $request->category != 'all') {
            $query->where('category_id', $request->category);
        }

        // Filter by brand
        if ($request->has('brand') && $request->brand != '') {
            $query->where('brand_id', $request->brand);
        }

        // Sorting
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'price_low':
                    $query->orderBy('list_price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('list_price', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $items = $query->get();

        // Transform the data
        $transformedItems = $items->map(function ($item) {
            return [
                'id' => $item->id,
                'item_code' => $item->item_code,
                'item_name' => $item->item_name,
                'description' => $item->description,
                'serial_number' => $item->serial_number,
                'batch_number' => $item->batch_number,
                'barcode' => $item->barcode,
                'supplier_id' => $item->supplier_id,
                'supplier_name' => $item->supplier ? $item->supplier->fname . ' ' . $item->supplier->lname : null,
                'brand_id' => $item->brand_id,
                'brand_name' => $item->brand?->name,
                'category_id' => $item->category_id,
                'category_name' => $item->category?->name,
                'sub_category_id' => $item->sub_category_id,
                'sub_category_name' => $item->subCategory?->name,
                'cost_price' => $item->cost_price,
                'list_price' => $item->list_price,
                'avg_price' => $item->avg_price,
                'unit_pack' => $item->unit_pack,
                'unit_of_measure' => $item->unit_of_measure,
                'group_code' => $item->group_code,
                'type_code' => $item->type_code,
                'price_level' => $item->price_level,
                'status' => $item->status,
                'is_active' => $item->status,
                'image_url' => $item->image_url,
                'created_by' => $item->created_by,
                'created_by_name' => $item->createdBy?->name,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $transformedItems
        ]);
    }

    public function getCategories(Request $request)
    {
        $categories = Category::whereNull('deleted_at')->get();

        // Add item count for each category
        $categories->map(function ($category) {
            $category->items_count = Item::where('category_id', $category->id)
                ->whereNull('deleted_at')
                ->count();
            return $category;
        });

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    public function getItemDetails($id)
    {
        $item = Item::with(['category', 'brand', 'supplier', 'subCategory'])
            ->where('status', true)
            ->whereNull('deleted_at')
            ->findOrFail($id);

        return response()->json([
            'id' => $item->id,
            'item_code' => $item->item_code,
            'item_name' => $item->item_name,
            'description' => $item->description,
            'serial_number' => $item->serial_number,
            'batch_number' => $item->batch_number,
            'barcode' => $item->barcode,
            'supplier_id' => $item->supplier_id,
            'supplier_name' => $item->supplier ? $item->supplier->fname . ' ' . $item->supplier->lname : null,
            'brand_id' => $item->brand_id,
            'brand_name' => $item->brand?->name,
            'category_id' => $item->category_id,
            'category_name' => $item->category?->name,
            'sub_category_id' => $item->sub_category_id,
            'sub_category_name' => $item->subCategory?->name,
            'cost_price' => $item->cost_price,
            'list_price' => $item->list_price,
            'avg_price' => $item->avg_price,
            'unit_pack' => $item->unit_pack,
            'unit_of_measure' => $item->unit_of_measure,
            'group_code' => $item->group_code,
            'type_code' => $item->type_code,
            'price_level' => $item->price_level,
            'status' => $item->status,
            'is_active' => $item->status,
            'image_url' => $item->image_url,
            'created_by' => $item->created_by,
            'created_by_name' => $item->createdBy?->name,
            'created_at' => $item->created_at,
            'updated_at' => $item->updated_at,
        ]);
    }
}
