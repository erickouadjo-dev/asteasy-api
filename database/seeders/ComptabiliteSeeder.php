<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Utility\Imports\CsvImport;
use App\Models\Comptabilite;

class ComptabiliteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $result = Excel::toArray(new CsvImport, public_path('files/excel/comptabilites.xlsx'));

      foreach ($result[0] as $key => $comptabite) {
        if($key == 0) {
          continue;
        }

        Comptabilite::create([
          'annee_fiscale'                        => $comptabite[0],
          'capitaux_propres'                     => $comptabite[1],
          'endettement_net'                      => $comptabite[2],
          'actif_circulant'                      => $comptabite[3],
          'actif_passant'                        => $comptabite[4],
          'besoins_financiers_entretien_routier' => $comptabite[5],
          'charge_fonctionnement'                => $comptabite[6],
          'ressources_mobilisees_hors_levee'     => $comptabite[7],
          'provisions_constituees'               => $comptabite[8],
          'engagements_prevus'                   => $comptabite[9],
          'ressources_mobilisees'                => $comptabite[10],
          'ressources_prevus'                    => $comptabite[11],
          'ressources_affectees_mobilisees'      => $comptabite[12],
          'budget_cible'                         => $comptabite[13],
          'created_at'                           => now(),
        ]);
      }
    }
}
