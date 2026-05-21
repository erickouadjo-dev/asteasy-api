<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Log;


class NotificationsController extends Controller
{
    //
    public function index()
    {
      try {
        $user= Utilisateur::findOrFail(Auth::user()->id);
        $notification = $user->notifications;  
        return response()->json(["notification"=>$notification, 'code_http'=>200]);
      } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
        Log::error('ExercicesFiscauxController@index a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
        return response()->json(['http_code'=>403, 'code'=>403, 'code_message'=>'Requête non autorisée.'], 403);
      }catch(\Exception $e){
        Log::error('NotificationController@index a échoué avec le message ' . $e->getMessage(), ['trace'=>$e->getTraceAsString()]);
      }
    }
}
