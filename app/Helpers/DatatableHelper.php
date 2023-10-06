<?php
namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class DatatableHelper {
	public static function make($cmd = null, $columns = [], $start, $length, $order = [], $search)
	{
		if(!$cmd) {
			throw new \Exception("cmd is required");
		}


        $dataOrder = [];
        foreach ($order as $row) {
            if ($row['column'] == 0) {
                $nestedOrder['column'] = 'id';
            }else{
                $nestedOrder['column'] = $columns[$row['column']]['name'];
            }
            $nestedOrder['dir'] = $row['dir'];

            $dataOrder[] = $nestedOrder;
        }


        $search_value = null;
        if (isset($search['value']) && $search['value']) {
            $search_value = strtolower($search['value']);
        }


		$totalData = $cmd->count();
		$totalFiltered = $cmd->count();


        if (!$search_value) {
            if ($length > 0) {
                $cmd->skip($start)->take($length);
            }

			// $cmd->latest('updated_at');
			foreach ($dataOrder as $row) {
				$cmd->orderBy($row['column'], $row['dir']);
			}
            // $cmd->orderBy($order['column'], $order['dir']);


		} else {
            // return [
            //     '$columns' => $columns,
            //     '$search_value' => $search_value,
            // ];
            $cmd->where(function($q) use($columns, $search_value) {
                foreach ($columns as $column) {
                    $field = $column['name'];
                    if (!in_array($field, ['number', 'action'])) {
                        $q->orWhereRaw("lower($field) like ?", ["%{$search_value}%"]);
                    }

                }
            });

			$totalFiltered = $cmd->count();
			if ($length > 0) {
				$cmd->skip($start)->take($length);
			}

			// $cmd->latest('updated_at');
			foreach ($dataOrder as $row) {
				$cmd->orderBy($row['column'], $row['dir']);
			}
            // $cmd->orderBy($order['column'], $order['dir']);

		}




		$rows = $cmd->get();

        $data = [
            'draw'  => intval(request()->draw),
            'data' => $rows,
            // 'totalData' => $totalData,
            // 'totalFiltered' => $totalFiltered,
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'order' => $dataOrder,
        ];
        if (env('APP_DEBUG')) {
            $data['sql'] = SqlHelper::getSqlWithBindings($cmd);
        }

        return $data;
	}
}
