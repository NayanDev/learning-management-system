<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Illuminate\Support\Facades\Auth;

class AuthController extends DefaultController
{
    protected $title;
    protected $generalUri;

    public function __construct()
    {
        $this->title = 'Login';
        $this->generalUri = 'login';
    }

    protected function login()
    {
        if (Auth::user()) {
            return redirect()->route('dashboard.index');
        }
        $data['title'] = $this->title;

        return view('frontend.login', $data);
    }
}
