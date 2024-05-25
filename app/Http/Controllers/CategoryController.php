<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Traits\GeneraleTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    use GeneraleTrait;
    public function index()
    {
        try {
            $categories = Category::all();
            return $this->successfulResponse(["data" => $categories]);
        } catch (\Throwable $th) {
            return $this->errorResponse(["data" => ["message" => "Internal Server Error"]], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                "name" => "required|string",
                "description" => "required|string",
            ];
            $data = $request->only(["name", "description"]);
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->errorResponse(["data" => ["messages" => $validator->messages()]], 400);
            }
            $category  = Category::create($data);
            return $this->successfulResponse(['data' => ["message" => "Category Created successfuly"]]);
        } catch (\Throwable $th) {
            return $this->errorResponse(["data" => ["message" => "Internal Server Error "]], 500);
        }
    }


    public function delete($id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete();
            return $this->successfulResponse(['data' => ["message" => "Category Deleted successfuly"]]);
        } catch (\Throwable $th) {
            return $this->errorResponse(["data" => ["messages" => "Not Found"]], 404);
        }
    }
}
