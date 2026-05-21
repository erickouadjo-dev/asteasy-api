<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Validator;
use Illuminate\Support\Facades\Mail as LaravelMail;

class Mail extends Model
{
    use HasFactory;

    protected $table = 'mails';
    protected $primaryKey = 'id';
    protected $guarded = ['updated_at'];
    public $timestamps = true;
    public $incrementing = true;

    const STATUT_EN_ATTENTE = 'EN_ATTENTE';
    const STATUT_ENVOYE = 'ENVOYE';
    const STATUT_NON_ENVOYE = 'NON_ENVOYE';

    const PRIORITE_IMMEDIATE = '0_IMMEDIATE';
    const PRIORITE_ELEVEE = '1_ELEVEE';
    const PRIORITE_NORMALE = '2_NORMALE';
    const PRIORITE_BASSE = '3_BASSE';

    //traite la file d'attente
    public static function envoyer(){
        try {
            set_time_limit(0);

            $result = [
                'code_http' => 200,
                'code_message' => 200
            ];

            //Log::info('traitement de la file d\'attente des mails...');

            $mails = Mail::where('statut', self::STATUT_EN_ATTENTE)
                ->orderBy('priorite', 'asc')
                ->take(25)
                ->get();

            if(count($mails)){
                foreach($mails as $mail){
                    $tracking_start = microtime(true);

                    try {
                        $parametres = json_decode($mail->parametres_mailable, true);

                        LaravelMail::to($mail->destinataire)->send(new $mail->classe_mailable($parametres));
                        if(count(LaravelMail::failures())){
                            $mail->statut = self::STATUT_NON_ENVOYE;
                            $mail->raison_echec_envoi = json_encode(LaravelMail::failures());
                            $mail->date_envoi = now();
                        }else{
                            $mail->statut = self::STATUT_ENVOYE;
                            $mail->date_envoi = now();
                        }
                    } catch (\Exception $e) {
                        $mail->statut = self::STATUT_NON_ENVOYE;
                        $mail->raison_echec_envoi = json_encode([
                            'message' => $e->getMessage()
                        ]);
                        $mail->date_envoi = now();

                        Log::error('Mail::envoyer a échoué pour le mail #' . $mail->id . ' avec le message ' . $e->getMessage(), [
                            'trace' => $e->getTraceAsString()
                        ]);
                    }

                    $mail->created_at = now();
                    $mail->save();

                    $tracking_time_elapsed_secs = microtime(true) - $tracking_start;
                    Log::info('L\'envoi du mail #' . $mail->id . ' a pris ' . $tracking_time_elapsed_secs . 's.');
                }
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Mail::envoyer a échoué avec le message ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        }
    }
}
