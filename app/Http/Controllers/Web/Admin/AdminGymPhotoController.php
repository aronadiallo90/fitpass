<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gym;
use App\Models\GymPhoto;
use App\Services\Interfaces\GymPhotoServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminGymPhotoController extends Controller
{
    public function __construct(private GymPhotoServiceInterface $photoService) {}

    public function store(Request $request, Gym $gym): RedirectResponse
    {
        $request->validate([
            'photo'    => ['required', 'image', 'mimes:jpeg,png,webp', 'max:4096'],
            'is_cover' => ['nullable', 'boolean'],
        ]);

        $this->photoService->store($gym, $request->file('photo'), (bool) $request->is_cover);

        return back()->with('success', 'Photo ajoutée.');
    }

    public function destroy(Gym $gym, GymPhoto $photo): RedirectResponse
    {
        abort_unless($photo->gym_id === $gym->id, 403);

        $this->photoService->delete($photo);

        return back()->with('success', 'Photo supprimée.');
    }

    public function setCover(Gym $gym, GymPhoto $photo): RedirectResponse
    {
        abort_unless($photo->gym_id === $gym->id, 403);

        $this->photoService->setCover($photo);

        return back()->with('success', 'Photo de couverture définie.');
    }
}
