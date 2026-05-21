<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Utility\Imports\CsvImport;
use App\Models\Programme;
use App\Models\Activite;
use App\Models\ActiviteProgramme;
use Carbon;

class ProgrammeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $result = Excel::toArray(new CsvImport, public_path('files/excel/donnees_historiques/ProgrammesFinal.xlsx'));



      foreach ($result[0] as $key => $programme) {
        if($key == 0) {
          continue;
        }

        $db_activite = Activite::where('id',26)->first();
        if(is_null($db_activite)) {
          Activite::create([ 
            'id'=>26,
            'libelle'=> 'Autres activités',
            'rubrique'=>6,
          ]);
        }

        $db_programme = Programme::find($programme[1]);

        if(!$db_programme) {
          Programme::create([
            'nom_programme' => $programme[1],
            'categorie_programme' => ($programme[0] == 'PROGRAMME D\'ENTRETIEN ROUTIER' ? 'programme_entretien_routier' : ( $programme[0] == 'PROGRAMME D\'INVESTISSEMENT' ? 'programme_investissement':'')),
            'date_debut' => Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($programme[3])),
            'date_fin' =>Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($programme[4])),
            'montant_initial_programme' => $programme[5],
            'date_validation_programme' => Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($programme[7])),
            'date_lancement_programme' => Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($programme[8])),
            'date_cloture_programme' => Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($programme[9])),
            'date_transmission_rapport_cloture_programme' => Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($programme[10])),
          ]);

          $programmes = Programme::orderBy('id','desc')->first();

          $activiteProgramme = ActiviteProgramme::create([
              'programme'=>$programmes->id,
              'activite' => 26,
              'montant' => 0,
              'observation'=>'Données Historiques RAS'
            ]);

        }
      }
    }
}
