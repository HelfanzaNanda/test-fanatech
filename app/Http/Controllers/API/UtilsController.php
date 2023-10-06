<?php

namespace App\Http\Controllers\API;

use App\Helpers\{DatatableHelper, NumberHelper, ResponseHelper};
use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Purchase;
use App\Models\Sales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UtilsController extends Controller
{
    public function generateNumber(Request $request)
    {
		$prefix = $request->get("prefix");

        try {
           $data = NumberHelper::generateNumber($prefix);
            return response()->json(ResponseHelper::success(data: $data), 200);

        } catch (\Throwable $th) {
            return response()->json(ResponseHelper::error(th: $th), 500);

        }
    }
}
