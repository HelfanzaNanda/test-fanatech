<?php

namespace App\Http\Controllers\API;

use App\Enum\RoleEnum;
use App\Helpers\AuthHelper;
use App\Helpers\DatatableHelper;
use App\Helpers\NumberHelper;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\PurchaseDetail;
use App\Models\Sales;
use App\Models\SalesDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SalesController extends Controller
{

    public function datatables(Request $request)
    {
        $columns = $request->get("columns", []);
        $start = $request->get("start");
		$length = $request->get("length");
		$order = $request->get("order");
		$search = $request->get("search");
        $currentRole = AuthHelper::getRole();
        $cmd = Sales::query();
        if ($currentRole == RoleEnum::SALES->value) {
            $cmd = $cmd->where("user_id", auth()->id());
        }
        $cmd = $cmd->with([
            "sales_details",
            "sales_details.inventory:id,name",
        ]);

        try {
            $data = DatatableHelper::make($cmd, $columns, $start, $length, $order, $search);
            return response()->json($data, 200);

        } catch (\Throwable $th) {
            return response()->json(ResponseHelper::error(th: $th), 500);

        }
    }

    public function store(Request $request)
    {

        $number = NumberHelper::generateNumber("SALES");
        $number = $number['number'];
        $date = now()->format("Y-m-d");

        DB::beginTransaction();


        // $validator = Validator::make($request->all(), [
        //     "inventory_id" => "required|array|min:3",
        //     "inventory_id.*" => "required|number",
        //     "qty" => "required|array|numeric",
        //     "qty.*" => "required|numeric",
        //     "price" => "required|array|numeric",
        //     "price.*" => "required|numeric",
        // ]);

        // if ($validator->fails()) {
        //     return response()->json(ResponseHelper::warning( validations: $validator->errors(), code: 422), 422);
        // }

        try {
            // $params = $validator->validated();
            $sales = Sales::create([
                "date" => $date,
                "number" => $number,
                "user_id" => auth()->id()
            ]);

            $items = $request->items;
            foreach ($items as $item) {

                $inventory = Inventory::query()->where("id", $item['inventory_id'])->first();
                if ($inventory) {
                    // $inventory->update([
                    //     'stock' => $inventory->stock - $item['qty']
                    // ]);

                    SalesDetail::create([
                        "sales_id" => $sales->id,
                        "inventory_id" => $item['inventory_id'],
                        "qty" => $item['qty'],
                        "price" => $inventory->price,
                    ]);
                }

            }
            DB::commit();
            return response()->json(ResponseHelper::success(), 200);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(ResponseHelper::error(th: $th), 500);
        }
    }


    public function find($id)
    {
        $data = Sales::query()->where("id", $id)
        ->with([
            "sales_details",
            "sales_details.inventory:id,name,stock",
        ])->first();
        if (!$data) {
            return response()->json(ResponseHelper::warning( message: 'data not found', code: 404), 404);
        }
        return response()->json(ResponseHelper::success(data: $data), 200);
    }

    public function update($id, Request $request)
    {
        DB::beginTransaction();

        try {
            // $params = $validator->validated();
            $sales = Sales::query()->where("id", $id)->with("sales_details")->first();
            if (!$sales) {
                return response()->json(ResponseHelper::warning( message: 'data not found', code: 404), 404);
            }
            $items = $request->items;
            $itemIds = [];
            if (count($items) > 0) {
                $itemIds = $sales->sales_details()->pluck("inventory_id")->toArray();
                $sales->sales_details()->delete();
            }


            foreach ($items as $item) {

                $inventory = Inventory::query()->where("id", $item['inventory_id'])->first();
                if ($inventory) {
                    // if (!in_array($inventory->id, $itemIds ?? [])) {
                    //     $inventory->update([
                    //         'stock' => $inventory->stock - $item['qty']
                    //     ]);
                    // }

                    SalesDetail::create([
                        "sales_id" => $sales->id,
                        "inventory_id" => $item['inventory_id'],
                        "qty" => $item['qty'],
                        "price" => $inventory->price,
                    ]);
                }

            }
            DB::commit();

            return response()->json(ResponseHelper::success(), 200);

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(ResponseHelper::error(th: $th), 500);
        }
    }

    public function delete($id)
    {
        $data = Sales::whereId($id)->first();
        if (!$data) {
            return response()->json(ResponseHelper::warning( message: 'data not found', code: 404), 404);
        }

        DB::beginTransaction();
        try {
            $data->sales_details()->delete();
            $data->delete();
            DB::commit();
            return response()->json(ResponseHelper::success(), 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(ResponseHelper::error(th: $th), 500);
        }
    }
}
