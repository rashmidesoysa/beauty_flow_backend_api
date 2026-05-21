<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Supplier;
use App\Models\Brand;
use App\Models\Category;
use App\Models\SubCategory;
use App\Http\Requests\ItemCreateRequest;
use App\Http\Requests\ItemUpdateRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::with(['supplier', 'brand', 'category', 'subCategory', 'createdBy'])
            ->orderBy('id', 'desc')
            ->get();

        $items = $items->map(function ($item) {
            return [
                'id' => $item->id,
                'item_code' => $item->item_code,
                'item_name' => $item->item_name,
                'description' => $item->description,
                'serial_number' => $item->serial_number,
                'batch_number' => $item->batch_number,
                'barcode' => $item->barcode,
                'supplier_id' => $item->supplier_id,
                'supplier_name' => $item->supplier?->fname . ' ' . $item->supplier?->lname,
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
            'data' => $items
        ]);
    }

    public function show($id)
    {
        $item = Item::with(['supplier', 'brand', 'category', 'subCategory', 'createdBy', 'updatedBy'])
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
            'supplier_name' => $item->supplier?->fname . ' ' . $item->supplier?->lname,
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

    public function create(ItemCreateRequest $request)
    {
        try {
            DB::beginTransaction();

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('ItemImages', 'public');
            }

            $item = Item::create([
                'item_code' => $this->generateItemCode(),
                'item_name' => $request->item_name,
                'description' => $request->description,
                'serial_number' => $request->serial_number,
                'batch_number' => $request->batch_number,
                'barcode' => $request->barcode,
                'supplier_id' => $request->supplier_id,
                'brand_id' => $request->brand_id,
                'category_id' => $request->category_id,
                'sub_category_id' => $request->sub_category_id,
                'cost_price' => $request->cost_price ?? 0,
                'list_price' => $request->list_price ?? 0,
                'avg_price' => $request->avg_price ?? 0,
                'unit_pack' => $request->unit_pack,
                'unit_of_measure' => $request->unit_of_measure,
                'group_code' => $request->group_code,
                'type_code' => $request->type_code,
                'price_level' => $request->price_level,
                'status' => true,
                'image_url' => $imagePath,
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item created successfully',
                'data' => $item
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Item creation failed',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function update(ItemUpdateRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $item = Item::findOrFail($id);

            $imagePath = $item->image_url;
            if ($request->hasFile('image')) {
                if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $request->file('image')->store('ItemImages', 'public');
            } else {
                $imagePath = null;
            }

            $item->update([
                'item_name' => $request->item_name,
                'description' => $request->description,
                'serial_number' => $request->serial_number,
                'batch_number' => $request->batch_number,
                'barcode' => $request->barcode,
                'supplier_id' => $request->supplier_id,
                'brand_id' => $request->brand_id,
                'category_id' => $request->category_id,
                'sub_category_id' => $request->sub_category_id,
                'cost_price' => $request->cost_price ?? 0,
                'list_price' => $request->list_price ?? 0,
                'avg_price' => $request->avg_price ?? 0,
                'unit_pack' => $request->unit_pack,
                'unit_of_measure' => $request->unit_of_measure,
                'group_code' => $request->group_code,
                'type_code' => $request->type_code,
                'price_level' => $request->price_level,
                'image_url' => $imagePath,
                'updated_by' => auth()->id(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item updated successfully',
                'data' => $item
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Item update failed',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function updateStatus($id)
    {
        try {
            DB::beginTransaction();

            $item = Item::findOrFail($id);
            $newStatus = !$item->status;

            $item->update([
                'status' => $newStatus,
                'updated_by' => auth()->id(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $newStatus ? 'Item activated successfully' : 'Item deactivated successfully',
                'data' => [
                    'id' => $item->id,
                    'status' => $item->status,
                    'is_active' => $item->status
                ]
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Status update failed',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $item = Item::findOrFail($id);

            $item->update([
                'deleted_by' => auth()->id(),
            ]);

            $item->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item deleted successfully'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Item deletion failed',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    private function generateItemCode()
    {
        $latest = Item::withTrashed()
            ->orderBy('id', 'desc')
            ->first();

        if (!$latest) {
            return 'ITEM00001';
        }

        $number = (int) substr($latest->item_code, 4);
        $nextNumber = $number + 1;

        $newCode = 'ITEM' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        while (Item::withTrashed()->where('item_code', $newCode)->exists()) {
            $nextNumber++;
            $newCode = 'ITEM' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        }

        return $newCode;
    }
}
