<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Traits\GeneraleTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    use GeneraleTrait;
    public function index()
    {
        try {
            $clients = Client::where("status", "available")->paginate(10);
            return ClientResource::collection($clients);
        } catch (\Throwable $th) {
            return $this->errorResponse(["data" => ["message" => "Internal Server Error"]], 500);
        }
    }

    public function show($id)
    {
        try {
            $client = Client::where("id", $id)->where("status", "available")->firstOrFail();
            return new ClientResource($client);
        } catch (\Throwable $th) {
            return $this->errorResponse(["data" => ["messages" => "Not Found "]], 404);
        }
    }

    public function store(Request $request)
    {
        $rules = [
            "name" => "required|string",
            "email" => "required|string",
            "phone" => "required|min:9|max:10",
        ];
        $data = $request->only(["name", "email", "phone"]);
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->errorResponse(["data" => ["messages" => $validator->messages()]], 400);
        }

        $client = Client::create($data);

        // Client::makeAllSearchable();
        $client->searchable();

        return $this->successfulResponse(['data' => ["message" => "Client Created successfuly"]]);
    }

    public function update(Request $request, $id)
    {
        try {

            $client = Client::where('id', $id)
                ->where('status', 'available')
                ->firstOrFail();
            $rules = [
                "name" => "required|string",
                "email" => "required|string",
                "phone" => "required|min:9|max:10",
            ];
            $data = $request->only(["name", "email", "phone"]);
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return $this->errorResponse(["data" => ["messages" => $validator->messages()]], 400);
            }
            $client =   $client->update($data);
            return $this->successfulResponse(['data' => ["message" => "Client Updated successfuly"]]);
        } catch (\Throwable $th) {
            return $this->errorResponse(["data" => ["messages" => "Not Found"]], 404);
        }
    }


    public function delete($id)
    {
        try {
            $client = Client::where('id', $id)
                ->where('status', 'available')
                ->firstOrFail();

            $orders = $client->orders;

            if (count($orders) > 0) {
                return $this->errorResponse(["data" => ["messages" => "Can't delete this client"]], 400);
            }

            $client->update(["status" => "unavailable"]);
            return $this->successfulResponse(['data' => ["message" => "Client Deleted successfuly"]]);
        } catch (\Throwable $th) {
            return $this->errorResponse(["data" => ["messages" => "Not Found"]], 404);
        }
    }

    public function search(Request $request)
    {
        if ($request->has("query")) {
            $query = $request->get("query");
            $clients  = Client::search($query)->paginate(10);
            return ClientResource::collection($clients);
        }
        return $this->errorResponse(["data" => ["messages" => "error query not send"]], 400);
    }
}
