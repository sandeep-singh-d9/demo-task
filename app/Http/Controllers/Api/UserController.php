<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role_id' => 'required',
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 404);
        }
        try {
            $user = Auth::user();
            if ($request->user()->tokenCan('admin')) {
                DB::beginTransaction();
                User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'role_id' => $request->role_id,
                    'password' => Hash::make($request->password)
                ]);
                DB::commit();
                return response()->json([
                    'message' => 'User Created Successfully!',
                    'success' => true
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Access denied',
                    'success' => false
                ], 500);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage(),
                'success' => false
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validateArray = [
            'role_id' => 'required',
            'name' => 'required',
        ];
        if ($request->has('password')) {
            $validateArray = array_merge($validateArray, ['password' => 'required|min:6']);
        }
        $validator = Validator::make($request->all(), $validateArray);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 404);
        }
        try {
            $user = Auth::user();
            if ($request->user()->tokenCan('admin')) {
                DB::beginTransaction();
                $data = [
                    'name' => $request->name,
                    'role_id' => $request->role_id,
                ];
                if ($request->has('password')) {
                    $validateArray = array_merge($validateArray, ['password' => Hash::make($request->password)]);
                }
                User::whereId($id)->update($data);
                DB::commit();
                return response()->json([
                    'message' => 'User Updated Successfully!',
                    'success' => true
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Access denied',
                    'success' => false
                ], 500);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage(),
                'success' => false
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            if ($request->user()->tokenCan('admin')) {
                DB::beginTransaction();
                User::whereId($id)->delete();
                DB::commit();
                return response()->json([
                    'message' => 'User Deleted Successfully!',
                    'success' => true
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Access denied',
                    'success' => false
                ], 500);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage(),
                'success' => false
            ], 500);
        }
    }
}
