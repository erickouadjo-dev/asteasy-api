<?php

namespace App\Utility\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\BpProjectionTarif;
use Log;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class ImporterProjectionTarif implements ToCollection, WithValidation, WithHeadingRow, WithCalculatedFormulas
{
    public function __construct($utilisateur)
    {
      $this->utilisateur = $utilisateur;
    }

    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {

      $this->BpProjectionTarif = collect();

       $nombre_tarif = 0;//vérifier si le nombre de tarif dans le fichier excel atteint 50
       $test = 0;//vérifier si les années des tarifs dans le fichier excel existent déjà dans la base de données 
       $valeur_annees = BpProjectionTarif::get();
       foreach ($collection as $key => $value) {
         $nombre_tarif += 1;
          foreach ($valeur_annees as $valeur_annee) { 
             if ($valeur_annee->annee == $value['annee']) {
               $test += 1;
             }
          }
       }
   
       if ($nombre_tarif == 50 && $test < 1) {
         
        foreach ($collection as $key => $value) {

          $projection_tarif = BpProjectionTarif::create([
            'annee' =>$value['annee'],
            'tarif_classe' =>$value['tarif_classe_1'],
            'classe_1' => $value['classe_1'],
            'classe_2' => $value['classe_2'],
            'classe_3' => $value['classe_3'],
            'classe_4' => $value['classe_4'],
           
          ]);
          
        }
      }
      
        $this->BpProjectionTarif->push($projection_tarif);
     
      return $this->BpProjectionTarif;
    }

    public function rules(): array
    {
      return [
        'annee' =>'numeric',
        'tarif_classe' =>'numeric',
        'classe_1' => 'numeric',
        'classe_2' => 'numeric',
        'classe_3' => 'numeric',
        'classe_4' => 'numeric',
      ];
    }
}

