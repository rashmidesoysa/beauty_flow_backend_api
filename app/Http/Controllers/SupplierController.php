<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\SupplierCreateRequest;
use App\Http\Requests\SupplierUpdateRequest;
use App\Http\Requests\UpdateSupplierRequest;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SupplierController extends Controller
{


    public function create(SupplierCreateRequest $request)
    {
        try {

            DB::beginTransaction();

            $supplier = Supplier::create([
                'supplier_code' => $this->generateSupplierCode(),
                'fname' => $request->fname,
                'lname' => $request->lname,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'tax_code' => $request->taxcode,
                'current_balance' => $request->curentBalance,
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
    public function show(Supplier $supplier)
    {
        //
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
    public function update(SupplierUpdateRequest $request, Supplier $supplier)
    {
        try {
            DB::beginTransaction();

            $supplier->update([
                'fname' => $request->fname,
                'lname' => $request->lname,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'tax_code' => $request->taxcode,
                'current_balance' => $request->curentBalance,
                'remarks' => $request->remarks,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Supplier updated successfully',
                'supplier' => $supplier
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        //
    }

    private function generateSupplierCode()
    {
        $latest = Supplier::lockForUpdate()
            ->orderBy('id', 'desc')
            ->first();

        if (!$latest) {
            return 'SUP00001';
        }

        $number = (int) substr($latest->supplier_code, 3);
        $next = $number + 1;

        return 'SUP' . str_pad($next, 5, '0', STR_PAD_LEFT);
    }
}
