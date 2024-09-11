<?php

namespace App\Services;

use Kreait\Firebase\Contract\Database;

interface ArchiveServiceCommunInterface
{
    public function archiveDette($dette);
    // public function getDatabase(): Database;
    // public function store($request);
}
