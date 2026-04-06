<?php

namespace App\Services;

use App\Models\User;
use App\Services\Interfaces\ProfilePhotoServiceInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProfilePhotoService implements ProfilePhotoServiceInterface
{
    // Dimensions cibles pour tous les avatars
    private const TARGET_SIZE = 400;
    private const JPEG_QUALITY = 85;

    public function store(User $user, UploadedFile $file): string
    {
        // Supprimer l'ancienne photo avant de sauvegarder la nouvelle
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $resized = $this->resizeToSquare($file);
        $path    = 'profiles/' . $user->id . '.jpg';

        Storage::disk('public')->put($path, $resized);

        $user->update(['profile_photo_path' => $path]);

        return $path;
    }

    public function delete(User $user): void
    {
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->update(['profile_photo_path' => null]);
        }
    }

    /**
     * Redimensionne l'image en carré 400×400 via GD natif PHP.
     * Gère JPG, PNG et WEBP sans dépendance externe.
     *
     * @return string contenu binaire JPEG
     */
    private function resizeToSquare(UploadedFile $file): string
    {
        $data = file_get_contents($file->getRealPath());
        $src  = imagecreatefromstring($data);

        if ($src === false) {
            throw new \RuntimeException('Impossible de lire le fichier image.');
        }

        $srcW = imagesx($src);
        $srcH = imagesy($src);

        // Recadrage centré pour obtenir un carré
        if ($srcW > $srcH) {
            $cropX = (int) (($srcW - $srcH) / 2);
            $cropY = 0;
            $cropSize = $srcH;
        } else {
            $cropX = 0;
            $cropY = (int) (($srcH - $srcW) / 2);
            $cropSize = $srcW;
        }

        $dst = imagecreatetruecolor(self::TARGET_SIZE, self::TARGET_SIZE);

        // Fond blanc pour les PNG transparents
        $white = imagecolorallocate($dst, 255, 255, 255);
        imagefill($dst, 0, 0, $white);

        imagecopyresampled(
            $dst, $src,
            0, 0,
            $cropX, $cropY,
            self::TARGET_SIZE, self::TARGET_SIZE,
            $cropSize, $cropSize
        );

        // Capturer l'image en mémoire (JPEG si disponible, PNG en fallback dev Windows)
        ob_start();
        if (function_exists('imagejpeg')) {
            imagejpeg($dst, null, self::JPEG_QUALITY);
        } else {
            imagepng($dst, null, 6);
        }
        $output = ob_get_clean();

        imagedestroy($src);
        imagedestroy($dst);

        return $output;
    }
}
