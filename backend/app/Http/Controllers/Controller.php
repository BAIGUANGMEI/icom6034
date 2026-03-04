<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'JobShare API',
    description: 'Recruitment Experiences Sharing Platform API Documentation',
    contact: new OA\Contact(email: 'admin@jobshare.com')
)]
#[OA\Server(
    url: 'http://localhost:8000',
    description: 'Local Development Server'
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Token',
    description: 'Enter your Sanctum token'
)]
#[OA\Tag(name: 'Auth', description: 'Authentication endpoints')]
#[OA\Tag(name: 'Posts', description: 'Post CRUD and browsing')]
#[OA\Tag(name: 'Comments', description: 'Comment management')]
#[OA\Tag(name: 'Tags', description: 'Tag browsing')]
abstract class Controller
{
    //
}
