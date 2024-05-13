<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiReponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ApiController extends Controller
{
    use ApiReponses;

    protected $policyClass;

    public function include(string $relationships): bool
    {
        $param = request()->get('include');
        if (!isset($param)) {
            return false;
        }

        $includeValues = explode(',', strtolower($param));

        return in_array(strtolower($relationships), $includeValues);
    }

    public function isAble($ability, $targetModel){
        return Gate::authorize($ability, [$targetModel, $this->policyClass]);
    }
}
