<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CustomerRegisterRequest;
use App\Http\Requests\CustomerLoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class CustomerAuthController extends Controller
{
    public function register(CustomerRegisterRequest $request)
    {
        try {

            DB::beginTransaction();

            $user = User::create([
                'name' => $request->fname . ' ' . $request->lname,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_type' => 'C'
            ]);


            $customer = Customer::create([
                'user_id' => $user->id,
                'cus_code' => $this->generateCustomerCode(),
                'fname' => $request->fname,
                'lname' => $request->lname,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
            ]);

            $token = $user->createToken('customer-token')->plainTextToken;

            DB::commit();

            return response()->json([
                'message' => 'Register Success',
                'token' => $token,
                'user' => $user
            ]);

        } catch (\Throwable $th) {
             DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Registration Failed',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function login(CustomerLoginRequest $request)
    {
        $user = User::where('email', $request->email)
            ->where('user_type', 'C')
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid Credentials'
            ], 401);
        }

        $token = $user->createToken('customer-login')->plainTextToken;

        return response()->json([
            'message' => 'Login Success',
            'token' => $token
        ]);
    }

    public function profile(Request $request)
    {
        return $request->user();
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout Success'
        ]);
    }

    private function generateCustomerCode()
    {
        $latest = Customer::lockForUpdate()
            ->orderBy('id', 'desc')
            ->first();

        if (!$latest) {
            return 'CUS00001';
        }

        $number = (int) substr($latest->cus_code, 3);
        $next = $number + 1;

        return 'CUS' . str_pad($next, 5, '0', STR_PAD_LEFT);
    }
}
