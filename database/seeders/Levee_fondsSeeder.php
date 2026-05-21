<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Utility\Imports\CsvImport;
use App\Models\Utilisateur;
use App\Models\LeveeFond;
use App\Models\Banque;

class Levee_fondsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $result = Excel::toArray(new CsvImport, public_path('files/excel/donnees_historiques/dettesFinal.xlsx'));

      foreach ($result[0] as $key => $levee_fond) {
        if($key == 0) {
          continue;
        }
        $banque = Banque::where('sigle',$levee_fond[2])->first();
        if ( $banque) {
          var_dump($banque->sigle);
        }
        
        LeveeFond::create([
          'nature_dette'=>	$levee_fond[0],
          'objet_dette'	=> $levee_fond[1],
          'preteur'	=> !is_null($banque) ? $banque->id : null,
          'montant_pret'=>	$levee_fond[3],
          'montant_tirages'=>	$levee_fond[4],
          'date_mise_place'=> is_numeric($levee_fond [5]) ? (\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($levee_fond [5])) : $levee_fond [5],
          'date_fin_remboursement'=> is_numeric($levee_fond [6]) ? (\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($levee_fond [6])) : $levee_fond [6],
          //paiement_periodique	$levee_fond[17]
          'duree_remboursement'=>	$levee_fond[7],
          'taux_interet'=>	$levee_fond[8],
          // taux_remboursement	$levee_fond[]
          'garantie_accordee'	=>$levee_fond[9],
          'nature_garantie' =>	$levee_fond[10],
          'periodicite' =>	$levee_fond[11],
          'differe_en_principal'=>	$levee_fond[12],
          'periode_differe_en_principal' =>	$levee_fond[13],
          'differe_en_interet'=>	$levee_fond[14],
          'periode_differe_en_interet'=>	$levee_fond[15],
          'differe_en_tob'=>	$levee_fond[16],
          'periode_differe_en_tob' =>	$levee_fond[17],
          'differe_en_assurence' =>	$levee_fond[18],
          'periode_differe_en_assurance' => $levee_fond[19]
        ]);
      }
    }
}

// nature_dette
// objet_dette
// preteur	
// montant_pret
// montant_tirages
// date_mise_place
// date_fin_remboursement
// duree_remboursement=>
// taux_interet
// garantie_accordee	
// nature_garantie
// periodicite 
// differe_en_principal
// periode_differe_en_principal 
// differe_en_interet
// periode_differe_en_interet
// differe_en_tob
// periode_differe_en_tob 
// differe_en_assurence 
// periode_differe_en_assurance 