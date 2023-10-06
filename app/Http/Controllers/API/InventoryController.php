<?php

namespace App\Http\Controllers\API;

use App\Helpers\DatatableHelper;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InventoryController extends Controller
{

    public function options(Request $request)
    {
        $term = trim($request->term);
        $options = Inventory::select("id", "name as text", "stock")
            ->where('name', 'LIKE',  '%' . $term. '%')
            ->orderBy('name', 'asc')->simplePaginate(10);
        $morePages = true;
        $pagination_obj = json_encode($options);
        if (empty($options->nextPageUrl())){
            $morePages = false;
        }
        $results = [
            "results" => $options->items(),
            "pagination" => [
                "more" => $morePages
            ]
        ];
        return response()->json($results);
    }

    public function datatables(Request $request)
    {
        $columns = $request->get("columns", []);
        $start = $request->get("start");
		$length = $request->get("length");
		$order = $request->get("order");
		$search = $request->get("search");
        $cmd = Inventory::query();

        try {
            $data = DatatableHelper::make($cmd, $columns, $start, $length, $order, $search);
            return response()->json($data, 200);

        } catch (\Throwable $th) {
            return response()->json(ResponseHelper::error(th: $th), 500);

        }
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'name' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(ResponseHelper::warning( validations: $validator->errors(), code: 422), 422);
        }

        try {
            $params = $validator->validated();
            Inventory::create($params);
            return response()->json(ResponseHelper::success(), 200);

        } catch (\Throwable $th) {
            return response()->json(ResponseHelper::error(th: $th), 500);
        }
    }


    public function find($id)
    {
        $data = Inventory::whereId($id)->first();
        if (!$data) {
            return response()->json(ResponseHelper::warning( message: 'data not found', code: 404), 404);
        }
        return response()->json(ResponseHelper::success(data: $data), 200);
    }

    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'name' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(ResponseHelper::warning( validations: $validator->errors(), code: 422), 422);
        }

        $data = Inventory::find($id);
        if (!$data) {
            return response()->json(ResponseHelper::warning( message: 'data not found', code: 404), 404);
        }


        try {
            $params = $validator->validated();
            $data->update($params);
            return response()->json(ResponseHelper::success(), 200);

        } catch (\Throwable $th) {
            return response()->json(ResponseHelper::error(th: $th), 500);
        }
    }

    public function delete($id)
    {
        $data = Inventory::whereId($id)->first();
        if (!$data) {
            return response()->json(ResponseHelper::warning( message: 'data not found', code: 404), 404);
        }

        try {
            $data->delete();
            return response()->json(ResponseHelper::success(), 200);
        } catch (\Throwable $th) {
            return response()->json(ResponseHelper::error(th: $th), 500);
        }
    }
}
