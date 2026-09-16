<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;
use OpenApi\Attributes as OAA;

#[OAA\Info(
    version: '1.0.0',
    title: 'LMS Admin API',
    description: 'API Documentation for the Laravel MongoDB MVC backend.'
)]
#[OAA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer'
)]
/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="LMS Admin API",
 *     description="API Documentation for the Laravel MongoDB MVC backend."
 * )
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer"
 * )
 */
abstract class Controller
{
    //
}
