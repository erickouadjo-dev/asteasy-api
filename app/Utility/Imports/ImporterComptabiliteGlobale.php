<?php

namespace App\Utility\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Transaction;
use App\Models\CompteBancaire;
use Carbon\Carbon;

class ImporterComptabiliteGlobale implements ToCollection, WithValidation, WithHeadingRow
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
      $this->transactions = collect();

      foreach ($collection as $key => $value) {
        if(empty($value['credit']) && empty($value['debit'])) {
          throw new \Exception("The given data was invalid", 1);
        }

        $compte_bancaire = CompteBancaire::where('numero', $value['numero_compte_bancaire'])->select('id')->first();

        $transaction = Transaction::create([
                          'compte_bancaire' => $compte_bancaire->id,
                          'libelle' => $value['libelle_des_operations'],
                          'operation' => Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['date_operation'])),
                          'debit' => $value['debit'],
                          'credit' => $value['credit'],
                        ]);

        $this->transactions->push($transaction);
      }

      return $this->transactions;
    }

    public function rules(): array
    {
        return [
          'numero_compte_bancaire' => 'required|integer',
          'libelle_des_operations' => 'required|string',
          'date_operation' => 'required',
          'debit' => 'nullable|numeric',
          'credit' => 'nullable|numeric',
        ];
    }
}
