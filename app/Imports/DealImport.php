<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;

class DealImport implements ToModel
{
    /**
    * @param Collection $collection
    */
    use Importable;
    public function model(array $row)
    {
        
    }
}
