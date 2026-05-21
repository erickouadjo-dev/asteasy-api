<?php

namespace App\Utility\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\Importable;
use App\Models\ComptabiliteGlobale;
use App\Models\PlanCompte;
use Carbon;
use Log;

class ImporterExecutionBudgetaire implements ToCollection, WithHeadingRow//, WithValidation
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
      $this->ComptabiliteGlobale = collect();
        
      foreach ($rows as $key => $value) {
        $compte = PlanCompte::where('compte','LIKE', $value['compte'].'%')->first();
        Log::info('test',['id_compte'=>$value['date']]);

        // if(is_null($compte)) {
        //   throw new \Exception("Compte ". $value['compte']." est inexistant", 1); 
        // }

        $comptabilite_globale = ComptabiliteGlobale::create([ 
            'titre'=> $value['titre'],
            'type'=> $value['type'],
            'sous_type'=> $value['sous_type'],
            'compte'=> is_null($compte) ? null : $compte->id,
            'libelle'=> $value['libelle'],
            'budget_annuel'=> $value['budget_annuel'],
            'debit'=> $value['debit'],
            'credit'=> $value['credit'],
            //'type_flux'=> $value['type_flux'],
            'date'=> !is_null($value['date']) ? Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['date'])) : '',
            'mois'=> $value['mois'],
            'gare_peage'=> $value['gare_peage'],
            'gare_pesage'=> $value['gare_pesage'],
            'classe_vehicule'=> $value['classe_vehicule'],
            'departement'=> 'CONTROLE_GESTION',
          ]);
          
        $this->ComptabiliteGlobale->push($comptabilite_globale);
      }
      return $this->ComptabiliteGlobale;
    }
    // public function rules(): array
    // {
    //   return [
    //         'titre'=> 'string',
    //         'reference'=> 'numeric',
    //         'libelle'=> 'string',
    //         'budget_annuel'=> 'numeric',
    //         'realisation'=> 'numeric',
    //         'type_flux'=> 'string',
    //         'annee'=> 'numeric',
    //       ];
    // }
}
