<?php

namespace App\Services;

use Kreait\Firebase\Contract\Database;

interface ArchiveServiceCommunInterface
{
    public function archiveDette($dette);
    public function getArchivedDettes(array $filter = []);
    public function getArchivedDettesByClient($clientId);
    public function getArchivedDetteDetailsById($detteId);
    public function restoreArchivedDetteById($detteId);
    public function restoreArchivedDettesByDate($date);
    public function restoreArchivedDettesByClient($clientId);
    // public function getDatabase(): Database;
    // public function store($request);
}
