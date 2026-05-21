<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Utility\Imports\CsvImport;
use App\Models\Sous_traitant;

class SousTraitantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $result=Excel::toArray(new CsvImport, public_path('files/excel/soustraitants.xlsx'));
        foreach ($result[0] as $key => $soustraitant) {
            if ($key == 0) {
                continue;
            }
            $db_soustraitant = Sous_traitant::find($soustraitant[0]);
            if (!$db_soustraitant) {
                Sous_traitant::create([
                    'id' => $soustraitant[0],
                    'libelle' => $soustraitant[1],
                ]);
            }
        }
    }
}
