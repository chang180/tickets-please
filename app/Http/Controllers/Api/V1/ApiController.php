<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiReponses;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    use ApiReponses;

    public function include(string $relationships): bool
    {
        $param = request()->get('include');
        if (!isset($param)) {
            return false;
        }

        $includeValues = explode(',', strtolower($param));

        return in_array(strtolower($relationships), $includeValues);
    }
}
