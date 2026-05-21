<?php

namespace App\Http\Controllers;

use App\Http\Requests\BrandRequest;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BrandController extends Controller
{
    //
    public function index()
    {
        $brands = Brand::with(['createdBy', 'category', 'subCategory'])->get();

        $brands = $brands->map(function ($brand) {
            return [
                'id' => $brand->id,
                'category_id' => $brand->category_id,
                'sub_category_id' => $brand->sub_category_id,
                'name' => $brand->name,
                'description' => $brand->description,
                'image_url' => $brand->image_url,
                'created_by' => $brand->created_by,
                'created_by_name' => $brand->createdBy?->name,
                'created_at' => $brand->created_at,
                'updated_at' => $brand->updated_at,
                'category' => $brand->category ? ['id' => $brand->category->id, 'name' => $brand->category->name] : null,
                'sub_category' => $brand->subCategory ? ['id' => $brand->subCategory->id, 'name' => $brand->subCategory->name] : null,
            ];
        });

        return response()->json(['data' => $brands]);
    }

    public function store(BrandRequest $request)
    {
        try {
            DB::beginTransaction();

            $imagePath = null;

            if ($request->hasFile('image')) {
                // create folder automatically if not exists
                $imagePath = $request->file('image')->store('BrandImages', 'public');
            }

            $brand = Brand::create([
                'name' => $request->name,
                'description' => $request->description,
                'image_url' => $imagePath,
                'category_id' => $request->category_id,
                'sub_category_id' => $request->sub_category_id,
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Brand created successfully',
                'data' => $brand
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create brand',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $brand = Brand::findOrFail($id);
        return response()->json($brand);
    }

    public function update(BrandRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $brand = Brand::findOrFail($id);

            $imagePath = $brand->image_url;

            if ($request->hasFile('image')) {
                // create folder automatically if not exists
                $imagePath = $request->file('image')->store('BrandImages', 'public');
            } else {
                $imagePath = null;
            }

            $brand->update([
                'name' => $request->name,
                'description' => $request->description,
                'image_url' => $imagePath,
                'category_id' => $request->category_id,
                'sub_category_id' => $request->sub_category_id,
                'updated_by' => auth()->id(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Brand updated successfully',
                'data' => $brand
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update brand',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $brand = Brand::findOrFail($id);
            $brand->update([
                'deleted_by' => auth()->id(),
            ]);
            $brand->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Brand deleted successfully'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete brand',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
