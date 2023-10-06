<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    private $icon = '
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" >
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
        </svg>
    ';

    private $menus = [
        [ 'label' => 'Purchase', 'name' => 'purchase.index' ],
    ];

    public function index()
    {
        $title = 'List of Purchase';
        return view('purchase.index', [
            'menus' => $this->menus,
            'icon' => $this->icon,
            'title' =>  $title,
        ]);
    }
    public function create()
    {
        $title = 'Create Purchase';
        return view('purchase.create', [
            'menus' => $this->menus,
            'icon' => $this->icon,
            'title' =>  $title,
        ]);
    }
    public function edit($id)
    {
        $title = 'Edit Purchase';
        return view('purchase.edit', [
            'id' => $id,
            'menus' => $this->menus,
            'icon' => $this->icon,
            'title' =>  $title,
        ]);
    }
    public function detail($id)
    {
        $title = 'Detail Purchase';
        return view('purchase.detail', [
            'id' => $id,
            'menus' => $this->menus,
            'icon' => $this->icon,
            'title' =>  $title,
        ]);
    }
}
