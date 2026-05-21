<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubCategoryRequest;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubCategoryController extends Controller
{
    //
    public function index()
    {
        $subCategories = SubCategory::with(['createdBy', 'category'])->get();

        $subCategories = $subCategories->map(function ($sub) {
            return [
                'id' => $sub->id,
                'category_id' => $sub->category_id,
                'name' => $sub->name,
                'description' => $sub->description,
                'image_url' => $sub->image_url,
                'created_by' => $sub->created_by,
                'created_by_name' => $sub->createdBy?->name,
                'created_at' => $sub->created_at,
                'updated_at' => $sub->updated_at,
                'category' => $sub->category ? ['id' => $sub->category->id, 'name' => $sub->category->name] : null,
            ];
        });

        return response()->json(['data' => $subCategories]);
    }


    public function store(SubCategoryRequest $request)
    {
        try {
            DB::beginTransaction();

            $imagePath = null;

            if ($request->hasFile('image')) {
                // create folder automatically if not exists
                $imagePath = $request->file('image')->store('SubCategoryImages', 'public');
            }

            $subCategory = SubCategory::create([
                'name' => $request->name,
                'description' => $request->description,
                'image_url' => $imagePath,
                'category_id' => $request->category_id,
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sub-category created successfully',
                'data' => $subCategory
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create sub-category',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $subCategory = SubCategory::findOrFail($id);
        return response()->json($subCategory);
    }

    public function update(SubCategoryRequest $request, $id)
    {

        try {
            DB::beginTransaction();

            $subCategory = SubCategory::findOrFail($id);

            $imagePath = $subCategory->image_url;

            if ($request->hasFile('image')) {
                // create folder automatically if not exists
                $imagePath = $request->file('image')->store('SubCategoryImages', 'public');
            } else {
                $imagePath = null;
            }

            $subCategory->update([
                'name' => $request->name,
                'description' => $request->description,
                'image_url' => $imagePath,
                'category_id' => $request->category_id,
                'updated_by' => auth()->id(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sub-category updated successfully',
                'data' => $subCategory
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update sub-category',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $subCategory = SubCategory::findOrFail($id);
            $subCategory->delete();
            $subCategory->update(['deleted_by' => auth()->id()]);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sub-category deleted successfully'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete sub-category',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
