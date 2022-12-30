<?php

namespace App\Http\Controllers\API;

use App\Http\Repositories\ResponseRepository;

class ApiController
{
    public function __construct(ResponseRepository $rp)
    {
        $this->responseRepository  = $rp;
    }
    public function info()
    {
        $info = [
            'info'   => config('info.api'),
        ];
        return $this->responseRepository->ResponseSuccess($info, 'List Fetch Successfully !');
    }
}
