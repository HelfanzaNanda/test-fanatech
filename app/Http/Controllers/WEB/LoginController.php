<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function index()
    {
        $title = 'login';
        return view('login.index', [
            // 'menus' => $this->menus,
            // 'icon' => $this->icon,
            'title' =>  $title,
        ]);
    }
}
