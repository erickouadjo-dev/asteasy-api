<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Models\Utilisateur;
use App\Http\Controllers\Api\V1\MarcheNotificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('amd-clear-cache', function(){
    Artisan::call('config:cache');
    return "cache cleared";
});

Route::get('amd-migrate', function(){
    Artisan::call('migrate');
    return Artisan::output();
});

Route::get('amd-seeder', function(){
    Artisan::call('db:seed');
    return Artisan::output();
});

Route::get('amd-passport', function(){
    Artisan::call('passport:install');
    return Artisan::output();
});

Route::get('init-admin', function(){
    $administrateur = new Utilisateur([
        'nom' => 'KOUADJO',
        'prenom' => 'Eric',
        'email' => 'administrateur@domaine.com',
        'identifiant' => 'Administrateur',
        'mot_de_passe' => Hash::make('adm1n1str@t3ur'),
        'type_utilisateur' => Utilisateur::TYPE_UTILISATEUR_ADMIN,
        'USER_TYPE_ID' => 1,
    ]);
    $administrateur->save();
    return [
        'email' => 'administrateur@domaine.com',
        'mot_de_passe' => 'adm1n1str@t3ur'
    ];
});

Route::get('init-user', function(){
    $utilisateur = new Utilisateur([
        'nom' => 'KOUADJO',
        'prenom' => 'Jean Eric',
        'email' => 'user@domaine.com',
        'identifiant' => 'Utilisateur',
        'mot_de_passe' => Hash::make('us3rP@ssw0rd'),
        'type_utilisateur' => Utilisateur::TYPE_UTILISATEUR_POWER_USER,
        'USER_TYPE_ID' => 2,
    ]);
    $utilisateur->save();
    return [
        'email' => 'user@domaine.com',
        'mot_de_passe' => 'us3rP@ssw0rd'
    ];
});



Route::get('amd-migrate-rollback', function(){
    Artisan::call('migrate:rollback');
    return Artisan::output();
});

Route::get('amd-schedule-list', function(){
    Artisan::call('schedule:list');
    return Artisan::output();
});

Route::get('amd-schedule-run', function(){
    Artisan::call('schedule:run');
    return Artisan::output();
});

Route::prefix('v1')->middleware(['cors'])->group(function(){
    // finaliser la creation du mot de passe depuis le lien email (sans session active)
    Route::post('/utilisateurs/{id}/finaliser-mot-de-passe', 'App\Http\Controllers\Api\V1\Utilisateurs\Utilisateur\MotDePasseController@finaliser');
});

Route::prefix('v1')->middleware(['cors', 'multi_authentication'])->group(function(){

//utilisateurs
    Route::post('/utilisateurs', 'App\Http\Controllers\Api\V1\UtilisateursController@store');
    Route::get('/utilisateurs', 'App\Http\Controllers\Api\V1\UtilisateursController@index');
    Route::get('/utilisateurs/{id}', 'App\Http\Controllers\Api\V1\Utilisateurs\Utilisateur\UtilisateursController@show');
    Route::put('/utilisateurs/{id}', 'App\Http\Controllers\Api\V1\Utilisateurs\Utilisateur\UtilisateursController@update');
    // Compatibilite clients legacy envoyant POST pour la mise a jour.
    Route::post('/utilisateurs/{id}', 'App\Http\Controllers\Api\V1\Utilisateurs\Utilisateur\UtilisateursController@update')->where('id', '[0-9]+');

/*
1-L’application Web cliente envoie une requête POST à{URLserveur}/utilisateurs/{id_utilisateur}/mot-de-passe
*/
    Route::post('/utilisateurs/authentifier', 'App\Http\Controllers\Api\V1\Utilisateurs\AuthentifierController@store');
    Route::post('/utilisateurs/deconnecter', 'App\Http\Controllers\Api\V1\Utilisateurs\DeconnecterController@store');

    Route::put('/utilisateurs/{id}/mot-de-passe', 'App\Http\Controllers\Api\V1\Utilisateurs\Utilisateur\MotDePasseController@update');
    // Compatibilite clients legacy envoyant POST pour la mise a jour du mot de passe.
    Route::post('/utilisateurs/{id}/mot-de-passe', 'App\Http\Controllers\Api\V1\Utilisateurs\Utilisateur\MotDePasseController@update')->where('id', '[0-9]+');

    Route::post('/utilisateurs/reinitialiser-mot-de-passe', 'App\Http\Controllers\Api\V1\Utilisateurs\ReinitialiserMotDePasseController@store');

    
    //uploads
    Route::post('/uploads', 'App\Http\Controllers\Api\V1\UploadsController@store');

    //departements
    Route::get('/departements', 'App\Http\Controllers\Api\V1\DepartementsController@index');

    //entreprises
    Route::post('/entreprises', 'App\Http\Controllers\Api\V1\EntreprisesController@store');
    Route::get('/entreprises', 'App\Http\Controllers\Api\V1\EntreprisesController@index');
    Route::get('/entreprises/{id}', 'App\Http\Controllers\Api\V1\EntreprisesController@show');
    Route::get('/entreprises/{id}/ressources', 'App\Http\Controllers\Api\V1\EntreprisesController@ressources');
    Route::put('/entreprises/{id}', 'App\Http\Controllers\Api\V1\EntreprisesController@update');
    Route::delete('/entreprises/{id}', 'App\Http\Controllers\Api\V1\EntreprisesController@destroy');

    //bases
    Route::post('/bases', 'App\Http\Controllers\Api\V1\BasesController@store');
    Route::get('/bases', 'App\Http\Controllers\Api\V1\BasesController@index');
    Route::get('/bases/{id}', 'App\Http\Controllers\Api\V1\BasesController@show');
    Route::put('/bases/{id}', 'App\Http\Controllers\Api\V1\BasesController@update');
    Route::delete('/bases/{id}', 'App\Http\Controllers\Api\V1\BasesController@destroy');

    //avancements
    Route::post('/avancements', 'App\Http\Controllers\Api\V1\AvancementsController@store');
    Route::get('/avancements', 'App\Http\Controllers\Api\V1\AvancementsController@index');
    Route::get('/avancements/{id}', 'App\Http\Controllers\Api\V1\AvancementsController@show');
    Route::put('/avancements/{id}', 'App\Http\Controllers\Api\V1\AvancementsController@update');
    Route::delete('/avancements/{id}', 'App\Http\Controllers\Api\V1\AvancementsController@destroy');

    //recurrences
    Route::post('/recurrences', 'App\Http\Controllers\Api\V1\RecurrencesController@store');
    Route::get('/recurrences', 'App\Http\Controllers\Api\V1\RecurrencesController@index');
    Route::get('/recurrences/{id}', 'App\Http\Controllers\Api\V1\RecurrencesController@show');
    Route::put('/recurrences/{id}', 'App\Http\Controllers\Api\V1\RecurrencesController@update');
    Route::delete('/recurrences/{id}', 'App\Http\Controllers\Api\V1\RecurrencesController@destroy');

    //statuts
    Route::post('/statuts', 'App\Http\Controllers\Api\V1\StatutsController@store');
    Route::get('/statuts', 'App\Http\Controllers\Api\V1\StatutsController@index');
    Route::get('/statuts/{id}', 'App\Http\Controllers\Api\V1\StatutsController@show');
    Route::put('/statuts/{id}', 'App\Http\Controllers\Api\V1\StatutsController@update');
    Route::delete('/statuts/{id}', 'App\Http\Controllers\Api\V1\StatutsController@destroy');

    //type-origine-actions
    Route::post('/type-origine-actions', 'App\Http\Controllers\Api\V1\TypeOrigineActionsController@store');
    Route::get('/type-origine-actions', 'App\Http\Controllers\Api\V1\TypeOrigineActionsController@index');
    Route::get('/type-origine-actions/{id}', 'App\Http\Controllers\Api\V1\TypeOrigineActionsController@show');
    Route::put('/type-origine-actions/{id}', 'App\Http\Controllers\Api\V1\TypeOrigineActionsController@update');
    Route::delete('/type-origine-actions/{id}', 'App\Http\Controllers\Api\V1\TypeOrigineActionsController@destroy');

    //gravite-risques
    Route::post('/gravite-risques', 'App\Http\Controllers\Api\V1\GraviteRisquesController@store');
    Route::get('/gravite-risques', 'App\Http\Controllers\Api\V1\GraviteRisquesController@index');
    Route::get('/gravite-risques/{id}', 'App\Http\Controllers\Api\V1\GraviteRisquesController@show');
    Route::put('/gravite-risques/{id}', 'App\Http\Controllers\Api\V1\GraviteRisquesController@update');
    Route::delete('/gravite-risques/{id}', 'App\Http\Controllers\Api\V1\GraviteRisquesController@destroy');

    //probabilite-risques
    Route::post('/probabilite-risques', 'App\Http\Controllers\Api\V1\ProbabiliteRisquesController@store');
    Route::get('/probabilite-risques', 'App\Http\Controllers\Api\V1\ProbabiliteRisquesController@index');
    Route::get('/probabilite-risques/{id}', 'App\Http\Controllers\Api\V1\ProbabiliteRisquesController@show');
    Route::put('/probabilite-risques/{id}', 'App\Http\Controllers\Api\V1\ProbabiliteRisquesController@update');
    Route::delete('/probabilite-risques/{id}', 'App\Http\Controllers\Api\V1\ProbabiliteRisquesController@destroy');

    //familles
    Route::post('/familles', 'App\Http\Controllers\Api\V1\FamillesController@store');
    Route::get('/familles', 'App\Http\Controllers\Api\V1\FamillesController@index');
    Route::get('/familles/{id}', 'App\Http\Controllers\Api\V1\FamillesController@show');
    Route::put('/familles/{id}', 'App\Http\Controllers\Api\V1\FamillesController@update');
    Route::delete('/familles/{id}', 'App\Http\Controllers\Api\V1\FamillesController@destroy');

    //agrements
    Route::post('/agrements', 'App\Http\Controllers\Api\V1\AgrementsController@store');
    Route::get('/agrements', 'App\Http\Controllers\Api\V1\AgrementsController@index');
    Route::get('/agrements/{id}', 'App\Http\Controllers\Api\V1\AgrementsController@show');
    Route::put('/agrements/{id}', 'App\Http\Controllers\Api\V1\AgrementsController@update');
    Route::delete('/agrements/{id}', 'App\Http\Controllers\Api\V1\AgrementsController@destroy');

    //formations
    Route::post('/formations', 'App\Http\Controllers\Api\V1\FormationsController@store');
    Route::get('/formations', 'App\Http\Controllers\Api\V1\FormationsController@index');
    Route::get('/formations/{id}', 'App\Http\Controllers\Api\V1\FormationsController@show');
    Route::put('/formations/{id}', 'App\Http\Controllers\Api\V1\FormationsController@update');
    Route::delete('/formations/{id}', 'App\Http\Controllers\Api\V1\FormationsController@destroy');

    //employe-formations
    Route::post('/employe-formations', 'App\Http\Controllers\Api\V1\EmployeFormationsController@store');
    Route::get('/employe-formations', 'App\Http\Controllers\Api\V1\EmployeFormationsController@index');
    Route::get('/employe-formations/{id}', 'App\Http\Controllers\Api\V1\EmployeFormationsController@show');
    Route::put('/employe-formations/{id}', 'App\Http\Controllers\Api\V1\EmployeFormationsController@update');
    Route::delete('/employe-formations/{id}', 'App\Http\Controllers\Api\V1\EmployeFormationsController@destroy');

    //nominations-employes
    Route::post('/nominations-employes', 'App\Http\Controllers\Api\V1\NominationsEmployesController@store');
    Route::get('/nominations-employes', 'App\Http\Controllers\Api\V1\NominationsEmployesController@index');
    Route::get('/nominations-employes/{id}', 'App\Http\Controllers\Api\V1\NominationsEmployesController@show');
    Route::put('/nominations-employes/{id}', 'App\Http\Controllers\Api\V1\NominationsEmployesController@update');
    Route::delete('/nominations-employes/{id}', 'App\Http\Controllers\Api\V1\NominationsEmployesController@destroy');

    //employes
    Route::post('/employes', 'App\Http\Controllers\Api\V1\EmployesController@store');
    Route::get('/employes', 'App\Http\Controllers\Api\V1\EmployesController@index');
    Route::get('/employes/{id}', 'App\Http\Controllers\Api\V1\EmployesController@show');
    Route::put('/employes/{id}', 'App\Http\Controllers\Api\V1\EmployesController@update');
    Route::delete('/employes/{id}', 'App\Http\Controllers\Api\V1\EmployesController@destroy');

    //notification
    Route::get('/marche-notification', 'App\Http\Controllers\Api\V1\NotificationsController@index');

    //plans
    Route::get('/plans', 'App\Http\Controllers\Api\V1\PlansController@index');
    Route::post('/plans', 'App\Http\Controllers\Api\V1\PlansController@store');
    Route::get('/plans/{id}', 'App\Http\Controllers\Api\V1\PlansController@show');
    Route::put('/plans/{id}', 'App\Http\Controllers\Api\V1\PlansController@update');
    Route::delete('/plans/{id}', 'App\Http\Controllers\Api\V1\PlansController@destroy');

    //modules
    Route::get('/modules', 'App\Http\Controllers\Api\V1\ModulesController@index');
    Route::post('/modules', 'App\Http\Controllers\Api\V1\ModulesController@store');
    Route::get('/modules/{id}', 'App\Http\Controllers\Api\V1\ModulesController@show');
    Route::put('/modules/{id}', 'App\Http\Controllers\Api\V1\ModulesController@update');
    Route::delete('/modules/{id}', 'App\Http\Controllers\Api\V1\ModulesController@destroy');

    //plans-modules
    Route::get('/plans-modules', 'App\Http\Controllers\Api\V1\PlansModulesController@index');
    Route::post('/plans-modules', 'App\Http\Controllers\Api\V1\PlansModulesController@store');
    Route::get('/plans-modules/{id}', 'App\Http\Controllers\Api\V1\PlansModulesController@show');
    Route::put('/plans-modules/{id}', 'App\Http\Controllers\Api\V1\PlansModulesController@update');
    Route::delete('/plans-modules/{id}', 'App\Http\Controllers\Api\V1\PlansModulesController@destroy');

    //fonctionnalites
    Route::get('/fonctionnalites', 'App\Http\Controllers\Api\V1\FonctionnalitesController@index');
    Route::post('/fonctionnalites', 'App\Http\Controllers\Api\V1\FonctionnalitesController@store');
    Route::get('/fonctionnalites/{id}', 'App\Http\Controllers\Api\V1\FonctionnalitesController@show');
    Route::put('/fonctionnalites/{id}', 'App\Http\Controllers\Api\V1\FonctionnalitesController@update');
    Route::delete('/fonctionnalites/{id}', 'App\Http\Controllers\Api\V1\FonctionnalitesController@destroy');

    //permissions
    Route::get('/permissions', 'App\Http\Controllers\Api\V1\PermissionsController@index');
    Route::post('/permissions', 'App\Http\Controllers\Api\V1\PermissionsController@store');
    Route::get('/permissions/{id}', 'App\Http\Controllers\Api\V1\PermissionsController@show');
    Route::put('/permissions/{id}', 'App\Http\Controllers\Api\V1\PermissionsController@update');
    Route::delete('/permissions/{id}', 'App\Http\Controllers\Api\V1\PermissionsController@destroy');

    //roles
    Route::get('/roles', 'App\Http\Controllers\Api\V1\RolesController@index');
    Route::post('/roles', 'App\Http\Controllers\Api\V1\RolesController@store');
    Route::get('/roles/{id}', 'App\Http\Controllers\Api\V1\RolesController@show');
    Route::put('/roles/{id}', 'App\Http\Controllers\Api\V1\RolesController@update');
    Route::delete('/roles/{id}', 'App\Http\Controllers\Api\V1\RolesController@destroy');

    //roles permissions
    Route::get('/roles-permissions', 'App\Http\Controllers\Api\V1\RolesPermissionsController@index');
    Route::post('/roles-permissions', 'App\Http\Controllers\Api\V1\RolesPermissionsController@store');
    Route::get('/roles-permissions/{id}', 'App\Http\Controllers\Api\V1\RolesPermissionsController@show');
    Route::put('/roles-permissions/{id}', 'App\Http\Controllers\Api\V1\RolesPermissionsController@update');
    Route::delete('/roles-permissions/{id}', 'App\Http\Controllers\Api\V1\RolesPermissionsController@destroy');

    //utilisateurs roles
    Route::get('/utilisateurs-roles', 'App\Http\Controllers\Api\V1\UtilisateursRolesController@index');
    Route::post('/utilisateurs-roles', 'App\Http\Controllers\Api\V1\UtilisateursRolesController@store');
    Route::get('/utilisateurs-roles/{id}', 'App\Http\Controllers\Api\V1\UtilisateursRolesController@show');
    Route::put('/utilisateurs-roles/{id}', 'App\Http\Controllers\Api\V1\UtilisateursRolesController@update');
    Route::delete('/utilisateurs-roles/{id}', 'App\Http\Controllers\Api\V1\UtilisateursRolesController@destroy');

    //abonnements
    Route::get('/abonnements', 'App\Http\Controllers\Api\V1\AbonnementsController@index');
    Route::post('/abonnements', 'App\Http\Controllers\Api\V1\AbonnementsController@store');
    Route::get('/abonnements/{id}', 'App\Http\Controllers\Api\V1\AbonnementsController@show');
    Route::put('/abonnements/{id}', 'App\Http\Controllers\Api\V1\AbonnementsController@update');
    Route::delete('/abonnements/{id}', 'App\Http\Controllers\Api\V1\AbonnementsController@destroy');

    //profils employes
    Route::get('/profils-employes', 'App\Http\Controllers\Api\V1\ProfilEmployesController@index');
    Route::post('/profils-employes', 'App\Http\Controllers\Api\V1\ProfilEmployesController@store');
    Route::get('/profils-employes/{id}', 'App\Http\Controllers\Api\V1\ProfilEmployesController@show');
    Route::put('/profils-employes/{id}', 'App\Http\Controllers\Api\V1\ProfilEmployesController@update');
    Route::delete('/profils-employes/{id}', 'App\Http\Controllers\Api\V1\ProfilEmployesController@destroy');
    
});