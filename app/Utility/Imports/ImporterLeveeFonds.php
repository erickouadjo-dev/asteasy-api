<?php

namespace App\Utility\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\LeveeFond;
use App\Models\Banque;
use Log;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Carbon;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\Importable;

class ImporterLeveeFonds implements ToCollection, WithHeadingRow
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
      $this->LeveeFond = collect();
        
      foreach ($rows as $key => $value) 
      {
        $sigle_banque = Banque::where('sigle', $value['sigle_banque'])->first();
        Log::info('test',['sigle_banque'=>$sigle_banque->id]);

        if(is_null($sigle_banque)) {
          throw new \Exception("Sigle ". $value['sigle_banque']." est inexistant", 1); 
        }

        $levee_fond = LeveeFond::create([ 

          'nature_dette'                  => $value['nature_dette'],
          'objet_dette'                   => $value['objet_dette'],
          'preteur'                       => $sigle_banque->id,
          'montant_pret'                  => $value['montant_pret'],
          'montant_tirages'               => $value['montant_tirages'],
          'date_mise_place'               => is_numeric($value ['date_mise_place']) ? (\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value ['date_mise_place'])) : $value['date_mise_place'],
          'date_fin_remboursement'        => is_numeric($value ['date_fin_remboursement']) ? (\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value ['date_fin_remboursement'])) : $value['date_fin_remboursement'],
          'duree_remboursement'           => $value['duree_remboursement'],
          'taux_interet'                  => $value['taux_interet'],
          'garantie_accordee'	            => $value['garantie_accordee'],
          'nature_garantie'               => $value['nature_garantie'],
          'periodicite'                   => $value['periodicite'],
          'differe_en_principal'          => $value['differe_en_principal'],
          'periode_differe_en_principal'  => $value['periode_differe_en_principal'],
          'differe_en_interet'            => $value['differe_en_interet'],
          'periode_differe_en_interet'    => $value['periode_differe_en_interet'],
          'differe_en_tob'                => $value['differe_en_tob'],
          'periode_differe_en_tob'        => $value['periode_differe_en_tob'],
          'differe_en_assurence'          => $value['differe_en_assurence'],
          'periode_differe_en_assurance'  => $value['periode_differe_en_assurance'],
          // 'tva'                           => $value['tva'],
          // 'ttc'                           => $value['ttc'],
          ]);
          
        $this->LeveeFond->push($levee_fond);
      }
      return $this->LeveeFond;
    }
    // public function rules(): array
    // {
    //   return [
    //     'facilite'                => 'string',
    //     'objet_dette'             => 'string',
    //     'sigle_banque'            => 'string',
    //     'montant_pret'            => 'numeric',
    //     'montant_tirages'         => 'numeric',
    //     'nature_pret'             => 'string',
    //     'maturite'                => 'integer',
    //     'periodicite'             => 'string',
    //     'paiement_periodique'     => 'numeric',
    //     'taux_commission'         => 'numeric',
    //     'duree_remboursement'     => 'integer',
    //     'taxe_operation_bourse'   => 'numeric',
    //     'taux_interet'            => 'numeric',
    //     'periode_differee'        => 'integer',
    //     'garanties_accordees'     => 'integer',
    //     'stock_dette'             => 'integer',
    //     'encours_rachat'          => 'integer',
    //   ];
    // }
}