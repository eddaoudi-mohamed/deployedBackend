<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Traits\GeneraleTrait;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    use GeneraleTrait;
    public function index()
    {
        try {
            $products = Product::where("statusExiste", "existe")->paginate(10);
            return ProductResource::collection($products);
        } catch (\Throwable $th) {
            return $this->errorResponse(["data" => ["message" => "Internal Server Error"]], 500);
        }
    }

    public function show($id)
    {
        try {
            $product = Product::where("id", $id)
                ->where("statusExiste", "existe")
                ->firstOrFail();
            return new ProductResource($product);
        } catch (\Throwable $th) {
            return $this->errorResponse(["data" => ["messages" => "Not Found "]], 404);
        }
    }

    public function store(Request $request)
    {
        try {

            $rules = [
                'name' => 'required|string',
                'description' => "required|string",
                "category_id" => "required|exists:categories,id",
                'price' => 'required|numeric|gt:0',
                "quantityPreUnit" => 'required|integer|gt:0',
                'image' => 'required|mimes:png,jpg,jpeg|max:2048'
            ];
            $data = $request->only(['name', 'description', "category_id", "price", "quantityPreUnit", "image"]);
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->errorResponse(["data" => ["messages" => $validator->messages()]], 400);
            }
            $path = $request->file('image')->store('images/products', 'public');
            $data['image'] = $path;
            $data["status"] = "available";
            $data["statusExiste"] = "existe";
            $data["unitsInStock"] = $data["quantityPreUnit"];
            $data["unitsOnOrder"] = 0;
            Product::create($data);
            return $this->successfulResponse(['data' => ["message" => "Product Created successfuly"]]);
        } catch (\Throwable $th) {
            return $this->errorResponse(["data" => ["message" => "Internal Server Error"]], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $product = Product::where("id", $id)
                ->where("statusExiste", "existe")
                ->firstOrFail();
            $rules = [
                'name' => 'required|string',
                'description' => "required|string",
                "category_id" => "required|exists:categories,id",
                'price' => 'required|numeric|gt:0',
                "quantityPreUnit" => 'required|integer|gt:0',
                'image' => 'mimes:png,jpg,jpeg|max:2048'
            ];
            $data = $request->only(['name', 'description', "category_id", "price", "quantityPreUnit", "image"]);
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->errorResponse(["data" => ["messages" => $validator->messages()]], 400);
            }
            if ($request->hasFile('image')) {
                if (Storage::exists('public/' . $product->image)) {
                    Storage::delete("public/" . $product->image);
                }
                $path = $request->file('image')->store('images/products', 'public');
                $data['image'] = $path;
            }
            if ($data['quantityPreUnit'] !== $product['quantityPreUnit']) {
                $data['unitsInStock'] = $product['unitsInStock'] + $data['quantityPreUnit'];
                $data['quantityPreUnit'] += $product['quantityPreUnit'];
            }

            $product->update($data);
            return $this->successfulResponse(['data' => ["message" => "Product Update successfuly"]]);
        } catch (\Throwable $th) {
            return $this->errorResponse(["data" => ["messages" => "Not Found " . $th->getMessage()]], 404);
        }
    }


    public function delete($id)
    {
        try {
            $product = Product::where("id", $id)
                ->where("statusExiste", "existe")
                ->firstOrFail();


            if ($product->unitsOnOrder > 0) {
                return $this->errorResponse(["data" => ["messages" => "Cant't delete this product"]], 404);
            }


            $product->update(['statusExiste' => "deleted"]);
            return $this->successfulResponse(['data' => ["message" => "Product Delete successfuly"]]);
        } catch (\Throwable $th) {
            return $this->errorResponse(["data" => ["messages" => "Not Found "]], 404);
        }
    }


    public function search(Request $request)
    {
        if ($request->has('query')) {
            try {
                $query = $request->get('query');
                $products = Product::search($query)->paginate(10);
                return ProductResource::collection($products);
            } catch (\Throwable $th) {
                return $this->errorResponse(["data" => ["message" => "Internal Server Error " . $th->getMessage()]], 500);
            }
        } else {
            return $this->errorResponse(["data" => ["messages" => "error query not send"]], 400);
        }
    }
}
