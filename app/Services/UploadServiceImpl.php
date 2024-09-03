<?php
namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Services\UploadService as UploadService;
class UploadServiceImpl
{

    public function upload(UploadedFile $file, $directory = 'images')
    {
        $fileName = time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path($directory), $fileName);

        return $fileName;
    }

    public function getBase64($filePath)
    {
        $path = public_path($filePath);
        $fileData = file_get_contents($path);
        dd(base64_encode($fileData)); 

        return base64_encode($fileData);
        // if (file_exists($path)) {
        //     dd($path);
        //     $fileData = file_get_contents($path);
        //     return base64_encode($fileData);
        // }

        return null;
    }
}