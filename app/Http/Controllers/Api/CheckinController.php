<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ValidateCheckinRequest;
use App\Http\Resources\CheckinResource;
use App\Models\Gym;
use App\Services\Interfaces\CheckinServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CheckinController extends Controller
{
    public function __construct(private readonly CheckinServiceInterface $checkinService) {}

    /**
     * Validation d'entrée via QR code (bornes autonomes en salle).
     */
    public function store(ValidateCheckinRequest $request): JsonResponse
    {
        $apiToken = $request->header('X-Gym-Token') ?? $request->input('gym_api_token');
        $gym      = Gym::where('api_token', $apiToken)->firstOrFail();

        $checkin = $this->checkinService->validate($request->qr_token, $gym);

        $statusCode = $checkin->isValid() ? 200 : 422;

        return response()->json([
            'data'    => new CheckinResource($checkin->load('gym')),
            'success' => $checkin->isValid(),
            'message' => $checkin->isValid()
                ? 'Entrée validée. Bonne séance !'
                : $checkin->failure_reason,
        ], $statusCode);
    }

    /**
     * Historique des checkins du membre authentifié.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $checkins = $this->checkinService->getMemberHistory($request->user());

        return CheckinResource::collection($checkins);
    }
}
