<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\Admin\ServerResource;
use App\Http\Responses\JsonResponse;
use App\Interfaces\ServerRepositoryInterface;
use App\Models\Server;
use Illuminate\Http\Request;

class ServerController extends ResourceController
{
    private ServerRepositoryInterface $serverRepository;

    public function __construct(ServerRepositoryInterface $serverRepository)
    {
        $this->serverRepository = $serverRepository;
    }

    public function index(Request $request)
    {
        if (!legacy_config('aggregator_mode_enabled')) {
            return new JsonResponse(['message' => 'Endpoint is unavailable when aggregator mode is disabled.'], 404);
        }

        $servers = $this->serverRepository->search(eagerStatistics: true);
        return ServerResource::collection($servers);
    }

    public function show(Server $server)
    {
        if (!legacy_config('aggregator_mode_enabled')) {
            return new JsonResponse(['message' => 'Endpoint is unavailable when aggregator mode is disabled.'], 404);
        }

        return new ServerResource($server);
    }
}
