<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
     * @OA\Info(
     *    title="HealthCare",
     *    version="1.0.0",
     *      description="Description removed for better illustration of structure.",
     *      @OA\Contact(
     *          email="admin@example.test",
     *          name="company",
     *          url="https://example.test"
     *      ),
     *      @OA\License(
     *          name="Apache 2.0",
     *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
     *      )
     * )
     * @OA\SecurityScheme(
     *     type="http",
     *     securityScheme="bearerAuth",
     *     scheme="bearer",
     *     bearerFormat="JWT"
     * )

    */

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

}
