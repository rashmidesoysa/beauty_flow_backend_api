<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('createdBy')->get();

        $categories = $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'description' => $category->description,
                'image_url' => $category->image_url,
                'created_by' => $category->created_by,
                'created_by_name' => $category->createdBy ? $category->createdBy->name : null,
                'created_at' => $category->created_at,
                'updated_at' => $category->updated_at,
            ];
        });

        return response()->json(['data' => $categories]);
    }

    public function store(CategoryRequest $request)
    {
        try {
            DB::beginTransaction();

            $imagePath = null;

            if ($request->hasFile('image')) {
                // create folder automatically if not exists
                $imagePath = $request->file('image')->store('CategoryImages', 'public');
            }

            $category = Category::create([
                'name' => $request->name,
                'description' => $request->description,
                'image_url' => $imagePath,
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully',
                'data' => $category
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create category',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $category = Category::findOrFail($id);
        return response()->json($category);
    }

    public function update(CategoryUpdateRequest $request, $id)
    {

        try {
            DB::beginTransaction();

            $category = Category::findOrFail($id);

            $imagePath = $category->image_url;

            if ($request->hasFile('image')) {
                // create folder automatically if not exists
                $imagePath = $request->file('image')->store('CategoryImages', 'public');
            } else {
                $imagePath = null;
            }

            $category->update([
                'name' => $request->name,
                'description' => $request->description,
                'image_url' => $imagePath,
                'updated_by' => auth()->id(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully',
                'data' => $category
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update category',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $category = Category::findOrFail($id);
            $category->delete();
            $category->update(['deleted_by' => auth()->id()]);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete category',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function restore($id)
    {
        // Code to restore a soft-deleted category
    }
}
