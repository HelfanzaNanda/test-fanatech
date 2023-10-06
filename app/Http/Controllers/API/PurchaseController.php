<?php

namespace App\Http\Controllers\API;

use App\Enum\RoleEnum;
use App\Helpers\AuthHelper;
use App\Helpers\DatatableHelper;
use App\Helpers\NumberHelper;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PurchaseController extends Controller
{

    public function datatables(Request $request)
    {
        $columns = $request->get("columns", []);
        $start = $request->get("start");
		$length = $request->get("length");
		$order = $request->get("order");
		$search = $request->get("search");
        $currentRole = AuthHelper::getRole();
        $cmd = Purchase::query();
        if ($currentRole == RoleEnum::PURCHASE->value) {
            $cmd = $cmd->where("user_id", auth()->id());
        }
        $cmd = $cmd->with([
            "purchase_details",
            "purchase_details.inventory:id,name",
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

        $number = NumberHelper::generateNumber("PURCHASE");
        $number = $number['number'];
        $date = now()->format("Y-m-d");


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
        DB::beginTransaction();

        try {
            // $params = $validator->validated();
            $purchase = Purchase::create([
                "date" => $date,
                "number" => $number,
                "user_id" => auth()->id()
            ]);

            $items = $request->items;
            foreach ($items as $item) {
                $inventory = Inventory::query()->where("id", $item['inventory_id'])->first();
                if ($inventory) {
                    $inventory->update([
                        'stock' => $inventory->stock + $item['qty']
                    ]);
                    PurchaseDetail::create([
                        "purchase_id" => $purchase->id,
                        "inventory_id" => $item['inventory_id'],
                        "qty" => $item['qty'],
                        "price" => $item['price'],
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
        $data = Purchase::query()->where("id", $id)
        ->with([
            "purchase_details",
            "purchase_details.inventory:id,name,stock",
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
            $purchase = Purchase::query()->where("id", $id)->with("purchase_details")->first();
            if (!$purchase) {
                return response()->json(ResponseHelper::warning( message: 'data not found', code: 404), 404);
            }
            $items = $request->items;
            $itemIds = [];
            if (count($items) > 0) {
                $itemIds = $purchase->purchase_details()->pluck("inventory_id")->toArray();
                $purchase->purchase_details()->delete();
            }


            foreach ($items as $item) {

                $inventory = Inventory::query()->where("id", $item['inventory_id'])->first();
                if ($inventory) {
                    if (!in_array($inventory->id, $itemIds ?? [])) {
                        $inventory->update([
                            'stock' => $inventory->stock + $item['qty']
                        ]);
                    }

                    PurchaseDetail::create([
                        "purchase_id" => $purchase->id,
                        "inventory_id" => $item['inventory_id'],
                        "qty" => $item['qty'],
                        "price" => $item['price'],
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
        $data = Purchase::whereId($id)->first();
        if (!$data) {
            return response()->json(ResponseHelper::warning( message: 'data not found', code: 404), 404);
        }

        DB::beginTransaction();
        try {
            $data->purchase_details()->delete();
            $data->delete();
            DB::commit();

            return response()->json(ResponseHelper::success(), 200);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(ResponseHelper::error(th: $th), 500);
        }
    }
}
