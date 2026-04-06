<?php

namespace App\Http\Controllers\Web\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfilePhotoRequest;
use App\Services\Interfaces\ProfilePhotoServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfilePhotoController extends Controller
{
    public function __construct(
        private readonly ProfilePhotoServiceInterface $photoService
    ) {}

    public function store(ProfilePhotoRequest $request): JsonResponse
    {
        $this->photoService->store($request->user(), $request->file('photo'));

        return response()->json([
            'photo_url' => $request->user()->fresh()->profile_photo_url,
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        $this->photoService->delete($request->user());

        return response()->json(['photo_url' => '']);
    }
}
