<?php

namespace App\Utility\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\CompteBancaire;
use App\Models\Banque;
use Log;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Carbon;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\Importable;

class ImporterComptesBancaires implements ToCollection, WithHeadingRow, WithValidation
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
      $this->Compte_bancaire = collect();
        
      foreach ($rows as $key => $value) 
      {
        $banque = Banque::where('sigle', $value['sigle_banque'])->first();
         Log::info('ddd',['sd2'=>$value['sigle_banque']]);
        if(is_null($banque)) {
          throw new \Exception("Le sigle ". $value['sigle_banque']." est inexistant", 1); 
        }

        $numero_compte = CompteBancaire::where('numero', $value['numero'])->first();
        Log::info('verif.',['numero compte'=>$numero_compte]);
        if(!is_null($numero_compte)) {
          throw new \Exception("Le numéro de compte ". $value['numero']." existe déjà", 1); 
        }

        $compte_bancaire = CompteBancaire::create([ 
          'numero' => $value['numero'],
          'banque'  => $banque->id,
          ]);
          
        $this->Compte_bancaire->push($compte_bancaire);
        
      }
      return $this->Compte_bancaire;
    }
    public function rules(): array
    {
      return [
        'numero'         => 'numeric',
        'sigle_banque'   => 'string',
      ];
    }
}