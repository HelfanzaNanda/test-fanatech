<?php
namespace App\Helpers;

use App\Models\Inventory;
use App\Models\Purchase;
use App\Models\Sales;

class NumberHelper {
    public static function generateNumber($prefix) {
        $result = null;

            switch ($prefix) {
                case 'SALES':

                    $pfx = "SLS";
                    $date = now()->format("Ymd");
                    $count = Sales::whereRaw('Date(created_at) = CURDATE()')->count();
                    $number = str_pad($count + 1, 4, '0', STR_PAD_LEFT);

                    $result = "$pfx-$date-$number";
                    $data = [
                        "number" => $result
                    ];
                    return $data;
                    // return response()->json(ResponseHelper::success(data: $data), 200);

                    break;
                case 'PURCHASE':
                    $pfx = "PRC";
                    $date = now()->format("Ymd");
                    $count = Purchase::whereRaw('Date(created_at) = CURDATE()')->count();
                    $number = str_pad($count + 1, 4, '0', STR_PAD_LEFT);

                    $result = "$pfx-$date-$number";
                    $data = [
                        "number" => $result
                    ];
                    return $data;
                    // return response()->json(ResponseHelper::success(data: $data), 200);
                    break;
                case 'INVENTORY':
                    $pfx = "INV";
                    $date = now()->format("Ymd");
                    $count = Inventory::whereRaw('Date(created_at) = CURDATE()')->count();
                    $number = str_pad($count + 1, 4, '0', STR_PAD_LEFT);

                    $result = "$pfx-$date-$number";
                    $data = [
                        "number" => $result
                    ];
                    return $data;
                    // return response()->json(ResponseHelper::success(data: $data), 200);
                    break;

                default:
                    return response()->json(ResponseHelper::warning(message: "prefix not found"), 404);
                    break;
            }
    }
}
