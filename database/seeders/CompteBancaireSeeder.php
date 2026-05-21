<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Utility\Imports\CsvImport;
use App\Models\Utilisateur;
use App\Models\CompteBancaire;

class CompteBancaireSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $result = Excel::toArray(new CsvImport, public_path('files/excel/comptes_bancaires.xlsx'));

      foreach ($result[0] as $key => $compte_bancaire) {
        if($key == 0) {
          continue;
        }

        $db_compte_bancaire = CompteBancaire::find($compte_bancaire[0]);

        if(!$db_compte_bancaire) {
          CompteBancaire::create([
            'id' => $compte_bancaire[0],
            'numero' => $compte_bancaire[1],      // Numero du compte bancaire
            'banque' => $compte_bancaire[2],      // ID de la banque
          ]);
        }
      }
    }
}
