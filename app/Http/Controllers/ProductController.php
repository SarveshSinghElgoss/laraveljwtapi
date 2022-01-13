<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    protected $user;
 
    public function __construct()
    {
        try
        {
            $this->user = JWTAuth::parseToken()->authenticate();
        }
        catch(JWTException $ex)
        {
            return response()->json([
                'success' => false,
                'message' => $ex->getMessage()
            ]);
        }        
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->user->products()->get();
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
        //Validate data
        $data = $request->only('name', 'company', 'price', 'quantity');
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'company' => 'required',
            'price' => 'required',
            'quantity' => 'required'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Request is valid, create new product
        $product = $this->user->products()->create([
            'name' => $request->name,
            'company' => $request->company,
            'price' => $request->price,
            'quantity' => $request->quantity
        ]);

        //Product created, return success response
        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product
        ], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = $this->user->products()->find($id);
    
        if (!$product) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, product not found.'
            ], 400);
        }
    
        return $product;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //Validate data
        $data = $request->only('name', 'company', 'price', 'quantity');
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'company' => 'required',
            'price' => 'required',
            'quantity' => 'required'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) 
        {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Request is valid, update product
        $product = $product->update([
            'name' => $request->name,
            'company' => $request->company,
            'price' => $request->price,
            'quantity' => $request->quantity
        ]);

        //Product updated, return success response
        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ], Response::HTTP_OK);
    }
}
