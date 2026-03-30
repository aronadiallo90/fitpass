<?php

namespace App\Services;

use App\Models\Gym;
use App\Models\GymPhoto;
use App\Services\Interfaces\GymPhotoServiceInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Stockage photos en local (disk public).
 * À remplacer par CloudinaryGymPhotoService en production
 * dès que CLOUDINARY_URL est configuré dans .env.
 */
class LocalGymPhotoService implements GymPhotoServiceInterface
{
    public function store(Gym $gym, UploadedFile $file, bool $isCover = false): GymPhoto
    {
        $path = $file->store("gyms/{$gym->id}/photos", 'public');

        $nextOrder = $gym->photos()->max('display_order') + 1;

        if ($isCover) {
            $gym->photos()->update(['is_cover' => false]);
        }

        return GymPhoto::create([
            'gym_id'            => $gym->id,
            'photo_url'         => Storage::disk('public')->url($path),
            'photo_storage_key' => $path, // chemin local relatif
            'display_order'     => $nextOrder,
            'is_cover'          => $isCover,
        ]);
    }

    public function delete(GymPhoto $photo): void
    {
        if ($photo->photo_storage_key) {
            Storage::disk('public')->delete($photo->photo_storage_key);
        }

        $photo->delete();
    }

    public function setCover(GymPhoto $photo): void
    {
        GymPhoto::where('gym_id', $photo->gym_id)->update(['is_cover' => false]);
        $photo->update(['is_cover' => true]);
    }

    public function reorder(Gym $gym, array $orderedIds): void
    {
        foreach ($orderedIds as $order => $id) {
            GymPhoto::where('id', $id)
                ->where('gym_id', $gym->id)
                ->update(['display_order' => $order]);
        }
    }
}
