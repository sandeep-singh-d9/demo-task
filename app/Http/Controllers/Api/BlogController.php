<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $blogs = Blog::select('*');
            if (Auth::user()->tokenCan('client')) {
                $blogs = $blogs->where('user_id', Auth::id());
            }
            $blogs = $blogs->get();
            return response()->json([
                'data' => $blogs,
                'success' => true
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'data' => [],
                'success' => false
            ], 500);
        }
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
            'description' => 'required',
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 404);
        }
        try {
            DB::beginTransaction();

            Blog::create([
                'user_id' => Auth::id(),
                'name' => $request->name,
                'description' => $request->description
            ]);
            DB::commit();

            return response()->json([
                'message' => 'Blog created successfully!',
                'success' => true
            ], 200);
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
        $validator = Validator::make($request->all(), [
            'description' => 'required',
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 404);
        }
        try {
            DB::beginTransaction();
            if (Auth::user()->tokenCan('client')) {
                $blog = Blog::whereId($id)->where('user_id', Auth::id())->first();
                if ($blog) {
                    Blog::whereId($id)->where('user_id', Auth::id())->update([
                        'user_id' => Auth::id(),
                        'name' => $request->name,
                        'description' => $request->description
                    ]);
                } else {
                    return response()->json([
                        'message' => 'Access denied',
                        'success' => false
                    ], 500);
                }
            } else {
                Blog::whereId($id)->update([
                    'name' => $request->name,
                    'description' => $request->description
                ]);
            }
            DB::commit();

            return response()->json([
                'message' => 'Blog updated successfully!',
                'success' => true
            ], 200);
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
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            if (Auth::user()->tokenCan('client')) {

                $blog = Blog::whereId($id)->where('user_id', Auth::id())->first();
                if ($blog) {
                    $blog->delete();
                } else {
                    return response()->json([
                        'message' => 'Access denied',
                        'success' => false
                    ], 500);
                }
            } else {
                Blog::whereId($id)->delete();
            }
            DB::commit();

            return response()->json([
                'message' => 'Blog deleted successfully!',
                'success' => true
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage(),
                'success' => false
            ], 500);
        }
    }
}
