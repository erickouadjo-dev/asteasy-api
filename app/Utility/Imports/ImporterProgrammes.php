<?php

namespace App\Utility\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\Importable;
use App\Models\Programme;
use App\Models\Activite;
use App\Models\ActiviteProgramme;
use Carbon;
use Log;

class ImporterProgrammes implements ToCollection, WithHeadingRow, WithValidation
{
    use Importable, SkipsFailures;
    public function __construct($utilisateur)
      {
        $this->utilisateur = $utilisateur;
      }
  
      /**
      * @param Collection $collection
      */
    
    public function collection(Collection $rows)
    {
      $this->Programme = collect();
        
      foreach ($rows as $key => $value) 
      {
        $db_programme = Programme::where('nom_programme',$value['nom_programme'])->get();
        if(!count($db_programme)) {
          $programme = Programme::create([ 
              'nom_programme' => $value['nom_programme'],
              'categorie_programme' => ($value['categorie_programme'] == 'PROGRAMME D\'ENTRETIEN ROUTIER' ? 'programme_entretien_routier' : ( $value['categorie_programme'] == 'PROGRAMME D\'INVESTISSEMENT' ? 'programme_investissement':'')),
              'date_debut' => ($value['date_debut']=='' ? null : Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['date_debut']))),
              'date_fin' => ($value['date_fin'] == '' ? null : Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['date_fin']))),
              'annee_exercice'=> $value['annee_exercice'],
              'montant_initial_programme' => $value['montant_initial_programme'],
              'date_validation_programme' => ($value['date_validation_programme'] == '' ? null : Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['date_validation_programme']))),
              'date_lancement_programme' => ($value['date_lancement_programme'] == '' ? null : Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['date_lancement_programme']))),
              'date_cloture_programme' => ($value['date_cloture_programme'] == '' ? null : Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['date_cloture_programme']))),
              'date_transmission_rapport_cloture_programme' => ($value['date_transmission_rapport_cloture_programme'] == '' ? null : Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['date_transmission_rapport_cloture_programme']))),
            ]);

            $programmes = Programme::orderBy('id','desc')->first();

            // $activites = Activite::get();
            // foreach ($activites as $activite) {
              $activiteProgramme = ActiviteProgramme::create([
                'programme'=>$programmes->id,
                'activite' => 26,
                'montant' => 0,
                'observation'=>'Données historiques importées'
              ]);
            //}
          $this->Programme->push($programme);
        }else {
          throw new \Exception("Le nom programme ". $value['nom_programme']." est déjà utiliser.", 1); 
        }
      }
      return $this->Programme;
    }
    public function rules(): array
    {
      return [
        'nom_programme' => 'string|max:100',
        'description' => 'string',
        'montant_initial_programme' => 'numeric',
      ];
    }
}

// categorie_programme	
// nom_programme 	
// date_debut 	
// date_fin	
// montant_initial_programme	
// date_validation_programme	
// date_lancement_programme	
// date_cloture_programme	
// date_transmission_rapport_cloture_programme	
