<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\SupplierCreateRequest;
use App\Http\Requests\SupplierStatusUpdateRequest;
use App\Http\Requests\SupplierUpdateRequest;
use App\Http\Requests\UpdateSupplierRequest;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SupplierController extends Controller
{

    public function index()
    {
        $suppliers = Supplier::with(['createdBy', 'updatedBy', 'deletedBy'])->get();

        // Add status to each supplier response
        $suppliers = $suppliers->map(function ($supplier) {
            return [
                'id' => $supplier->id,
                'sup_code' => $supplier->sup_code,
                'fname' => $supplier->fname,
                'lname' => $supplier->lname,
                'email' => $supplier->email,
                'phone' => $supplier->phone,
                'address' => $supplier->address,
                'city' => $supplier->city,
                'Taxcode' => $supplier->Taxcode,
                'currentBalance' => $supplier->currentBalance,
                'remarks' => $supplier->remarks,
                'is_active' => $supplier->is_active ?? true,
                'created_by' => $supplier->created_by,
                'created_by_name' => $supplier->createdBy?->name,
                'created_at' => $supplier->created_at,
                'updated_at' => $supplier->updated_at,
            ];
        });

        return response()->json([
            'message' => 'Suppliers retrieved successfully',
            'suppliers' => $suppliers
        ]);
    }

    public function create(SupplierCreateRequest $request)
    {
        try {

            DB::beginTransaction();

            $supplier = Supplier::create([
                'sup_code' => $this->generateSupplierCode(),
                'fname' => $request->fname,
                'lname' => $request->lname,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'Taxcode' => $request->Taxcode,
                'currentBalance' => $request->curentBalance,
                'remarks' => $request->remarks,
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Supplier created successfully',
                'supplier' => $supplier,
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Supplier creation failed',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function store($request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $supplier = Supplier::with(['createdBy', 'updatedBy'])->findOrFail($id);

        return response()->json([
            'id' => $supplier->id,
            'sup_code' => $supplier->sup_code,
            'fname' => $supplier->fname,
            'lname' => $supplier->lname,
            'email' => $supplier->email,
            'phone' => $supplier->phone,
            'address' => $supplier->address,
            'city' => $supplier->city,
            'Taxcode' => $supplier->Taxcode,
            'currentBalance' => $supplier->currentBalance,
            'remarks' => $supplier->remarks,
            'is_active' => $supplier->is_active,
            'status' => $supplier->is_active ? 'active' : 'inactive',
            'created_by' => $supplier->created_by,
            'created_by_name' => $supplier->createdBy?->name,
            'created_at' => $supplier->created_at,
            'updated_at' => $supplier->updated_at,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SupplierUpdateRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $supplier = Supplier::findOrFail($id);

            $supplier->update([
                'fname' => $request->fname,
                'lname' => $request->lname,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'Taxcode' => $request->Taxcode,
                'currentBalance' => $request->currentBalance,
                'remarks' => $request->remarks,
                'updated_by' => auth()->id(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Supplier updated successfully',
                'supplier' => $supplier,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Supplier update failed',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function updateSupplierStatus(SupplierStatusUpdateRequest  $request, $id)
    {
        try {
            DB::beginTransaction();

            $supplier = Supplier::findOrFail($id);

            $isActive = $request->status === 'active' ? true : false;

            $supplier->update([
                'is_active' => $isActive,
                'updated_by' => auth()->id(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Supplier status updated successfully',
                'supplier' => [
                    'id' => $supplier->id,
                    'is_active' => $supplier->is_active,
                    'fname' => $supplier->fname,
                    'lname' => $supplier->lname,
                ]
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Supplier status update failed',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $supplier = Supplier::findOrFail($id);
            $supplier->update([
                'deleted_by' => auth()->id(),
            ]);

            $supplier->delete();

            DB::commit();

            return response()->json([
                'message' => 'Supplier deleted successfully',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Supplier deletion failed',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    private function generateSupplierCode()
    {
        $latest = Supplier::withTrashed()
            ->where('sup_code', 'LIKE', 'SUP%')
            ->orderByRaw('CAST(SUBSTRING(sup_code, 4, LEN(sup_code)) AS INT) DESC')
            ->first();

        if (!$latest) {
            return 'SUP00001';
        }

        $number = (int) substr($latest->sup_code, 3);
        $nextNumber = $number + 1;

        $newCode = 'SUP' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        while (Supplier::withTrashed()->where('sup_code', $newCode)->exists()) {
            $nextNumber++;
            $newCode = 'SUP' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        }

        return $newCode;
    }
}
