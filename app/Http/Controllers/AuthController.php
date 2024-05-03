<?php

namespace App\Http\Controllers;

use App\Traits\ApiReponses;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiReponses;
    public function login(){
        return $this->ok('Hello Trait!');
    }
}
