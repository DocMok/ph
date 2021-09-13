<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @OA\Info(version="1.0.0", title="PartnerHub",description="PartnerHub API"),
     * @OA\SecurityScheme(securityScheme="Authorization",type="apiKey",name="Authorization",in="header")
     *
     */

    /**
     * @OA\Schema(schema="success",
     *   @OA\Property(property="success",type="boolean",example=true),
     *   @OA\Property(property="errors_message",type="string",example=null),
     *   @OA\Property(property="data",type="string",example="success"),
     * )
     */

    /**
     * @OA\Schema(schema="errorResponse",
     *   @OA\Property(property="success",type="boolean",example=false),
     *   @OA\Property(property="errors_message",type="string",example="you can not get it"),
     *   @OA\Property(property="data",type="string",example=null),
     * )
     */
}
