<?php

namespace App\Utility\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use App\Models\ComptabiliteGlobale;
use App\Models\PlanCompte;
use Carbon;
use Log;

class ImporterJournaux implements ToCollection, WithHeadingRow
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
        
      foreach ($rows as $key => $value) 
      {

        $compte = PlanCompte::where('compte', $value['compte'])->first();
        Log::info('test',['id_compte'=>$compte]);

        // if(is_null($compte)) {
        //   throw new \Exception("Compte ". $value['compte']." est inexistant", 1); 
        // }

        $comptabilite_globale = ComptabiliteGlobale::create([ 
          'site'=> $value['site'],
          'base'=> $value['base'],
          'date'=>!is_null($value['date_n_ordre']) ? Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['date_n_ordre'])) : '',
          'nro_ordre'=> substr($value['date_n_ordre'],13),
          'nro_piece'=> $value['nro_piece'],
          'compte'=>is_null($compte) ? null : $compte->id,
          'libelle'=> $value['libelle'],
          'debit'=> $value['debit'],
          'credit'=> $value['credit'],
          'monnaie'=> $value['monnaie'],
          'montant_en_devise'=> $value['montant_en_devise'],
          'montant_en_report'=> $value['montant_en_report'],
          'tache'=> $value['tache'],
          'poste'=> $value['poste'],
          'financement_categorie'=> $value['financement_categorie'],
          'tronc'=> $value['tronc'],
          'journal'=> $value['journal'], 
          'marche'=> $value['marche'],
          'mode'=> $value['mode'],
          'releve'=> $value['releve'],
          'a_campagne'=> $value['a_campagne'], 
          's_compte'=> $value['s_compte'],
          'nro_drf'=> $value['nro_drf'],
          'ref_paiement'=> $value['ref_paiement'],
          'benef_paiement'=> $value['benef_paiement'],
          'departement'=> 'COMPTABILITE',
          ]);
          
        $this->ComptabiliteGlobale->push($comptabilite_globale);
      }
      return $this->ComptabiliteGlobale;
    }
    // public function rules(): array
    // {
    //   return [
    //     'Débit'=>'numeric',
    //     'Crédit'=>'numeric',
    //   ];
    // }
}
