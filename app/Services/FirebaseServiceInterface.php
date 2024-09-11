<?php
namespace App\Services;

use Kreait\Firebase\Contract\Database;

interface FirebaseServiceInterface 
{
    
public function getDatabase(): Database;
public function store($request);
public function archiveDette($dette);
}