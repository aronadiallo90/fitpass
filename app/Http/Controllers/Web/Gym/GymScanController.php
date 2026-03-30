<?php

namespace App\Http\Controllers\Web\Gym;

use App\Http\Controllers\Controller;
use App\Services\Interfaces\CheckinServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GymScanController extends Controller
{
    public function __construct(private readonly CheckinServiceInterface $checkinService) {}

    public function validate(Request $request): JsonResponse
    {
        $request->validate(['qr_token' => ['required', 'string']]);

        $gym = $request->user()->gym;

        if (!$gym) {
            return response()->json(['message' => 'Aucune salle associée à ce compte.'], 403);
        }

        $checkin = $this->checkinService->validate($request->qr_token, $gym);

        return response()->json([
            'data'    => [
                'status'      => $checkin->status,
                'member_name' => $checkin->user?->name,
                'gym_name'    => $gym->name,
            ],
            'success' => $checkin->isValid(),
            'message' => $checkin->isValid()
                ? 'Entrée validée. Bonne séance !'
                : $checkin->failure_reason,
        ], $checkin->isValid() ? 200 : 422);
    }
}
