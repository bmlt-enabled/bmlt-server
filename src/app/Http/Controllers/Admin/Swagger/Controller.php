<?php

namespace App\Http\Controllers\Admin\Swagger;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    description: 'BMLT Admin API Documentation',
    title: 'BMLT',
    license: new OA\License(
        name: 'MIT',
        url: 'https://github.com/bmlt-enabled/bmlt-root-server/blob/main/LICENSE'
    )
)]
#[OA\SecurityScheme(
    securityScheme: 'bmltToken',
    type: 'oauth2',
    flows: [
        new OA\Flow(
            tokenUrl: 'api/v1/auth/token',
            refreshUrl: 'api/v1/auth/refresh',
            flow: 'password',
            scopes: []
        ),
    ]
)]
class Controller
{
}
