<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Validator;
use App\Traits\BelongsToTenant;

class TraceActivite extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'trace_activites';
    protected $primaryKey = 'id';
    protected $guarded = ['updated_at'];
    protected $casts = [
        'ENTREPRISE_ID' => 'integer',
    ];
    public $timestamps = true;
    public $incrementing = true;

    const OPERATION_AJOUT = 'AJOUT';
    const OPERATION_MODIFICATION = 'MODIFICATION';
    const OPERATION_LECTURE = 'LECTURE';
    const OPERATION_SUPPRESSION = 'SUPPRESSION';
    const OPERATION_AUTRE = 'AUTRE';

    //obtenir l'initiateur de l'activité
    public function initiateur(){
        try{
            return $this->belongsTo(Utilisateur::class, 'utilisateur', 'id');
        }catch(\Exception $e){
            Log::error('TraceActivite::initiateur a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
        }
    }
}
