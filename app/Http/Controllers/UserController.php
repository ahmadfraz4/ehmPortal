<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(){
        $data = User::whereNot('id', Auth::user()->id)->paginate(10);
        return view('users', ['data' => $data]);
    }
}
