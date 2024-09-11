<?php
namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use App\Services\UploadService as UploadService;
use Illuminate\Support\Facades\Log;

class UploadServiceImpl implements UploadService
{

    public function upload(UploadedFile $file, $directory = 'images')
    {
        $fileName = time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path($directory), $fileName);

        return $fileName;
    }
    public function getBase64(string $filePath): string
    {
        // dd($filePath);
        // Vérifier si le fichier existe
        if (Storage::disk('public')->exists($filePath)) {
            // Obtenir le contenu du fichier
            $fileContent = Storage::disk('public')->get($filePath);

            // Retourner le contenu encodé en base64
            return base64_encode($fileContent);
        }

        // Lever une exception si le fichier n'existe pas
        throw new \Exception('Fichier non trouvé : ' . $filePath);
    }

    public function encodePhotoToBase64($photoUrl)
    {
        if ($photoUrl) {
            try {
                $imageData = file_get_contents($photoUrl);
                $imageExtension = pathinfo(parse_url($photoUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
                return 'data:image/' . $imageExtension . ';base64,' . base64_encode($imageData);
            } catch (\Exception $e) {
                Log::error('Erreur lors de l\'encodage de la photo en base64 : ' . $e->getMessage());
                return null;
            }
        }
        return null;
    }
}