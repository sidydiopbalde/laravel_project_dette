<?php
namespace App\Services;

interface pdfServiceInterface 
{
    
public function generatePdf(string $view, array $data, string $pdfPath);
}