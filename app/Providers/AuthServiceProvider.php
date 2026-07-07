<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;
use App\Policies\UtilisateursPolicy;
use App\Policies\ProgrammesPolicy;
use App\Policies\Programmes\ProgrammePolicy;
use App\Policies\Utilisateurs\UtilisateurPolicy;
use App\Policies\Utilisateurs\ReinitialiserMotDePassePolicy;
use App\Policies\Utilisateurs\AuthentifierPolicy;
use App\Policies\Utilisateurs\DeconnecterPolicy;
use App\Utility\PolicyResources\Utilisateurs\Authentifier as AuthentifierResource;
use App\Utility\PolicyResources\Utilisateurs\Deconnecter as DeconnecterResource;
use App\Utility\PolicyResources\Utilisateurs as UtilisateursResource;
use App\Utility\PolicyResources\Programmes as ProgrammesResource;
use App\Utility\PolicyResources\Utilisateurs\ReinitialiserMotDePasse as ReinitialiserMotDePasseResource;
use App\Models\Utilisateur as UtilisateurResource;
use App\Models\Programme as ProgrammeResource;
use App\Policies\RubriquesPolicy;
use App\Utility\PolicyResources\Rubriques as RubriquesResource;
use App\Policies\Rubriques\RubriquePolicy;
use App\Models\Rubrique as RubriqueResource;
use App\Policies\Rubriques\Rubrique\ActivitesPolicy;
use App\Utility\PolicyResources\Rubriques\Rubrique\Activites as RubriquesRubriqueActivitesResource;
use App\Policies\Rubriques\Rubrique\Activites\ActivitePolicy;
use App\Models\Activite as ActiviteResource;
use App\Utility\PolicyResources\Activite_Programme as Activite_ProgrammeResource;
use App\Policies\Programmes\Activite_ProgrammePolicy;
use App\Policies\UploadsPolicy;
use App\Utility\PolicyResources\Uploads as UploadsResource;
use App\Policies\DepartementsPolicy;
use App\Utility\PolicyResources\Departements as DepartementsResource;
use App\Policies\PrestatairesPolicy;
use App\Utility\PolicyResources\Prestataires as PrestatairesResource;
use App\Policies\MarchesPolicy;
use App\Utility\PolicyResources\Marches as MarchesResource;
use App\Policies\Marches\MarchePolicy;
use App\Models\Marche as MarcheResource;
use App\Utility\PolicyResources\Sous_traitants as Sous_traitantsResource;
use App\Policies\Sous_traitantsPolicy;
use App\Models\Sous_traitant as Sous_traitantResource;
use App\Policies\Sous_traitants\Sous_traitantPolicy;
use App\Policies\DecomptesPolicy;
use App\Utility\PolicyResources\Decomptes as DecomptesResource;
use App\Policies\Decomptes\DecomptePolicy;
use App\Models\Decompte as DecompteResource;
use App\Policies\Decomptes\Decompte\ValiderPolicy;
use App\Utility\PolicyResources\Decomptes\Decompte\Valider as DecomptesDecompteValiderResource;
use App\Policies\DecaissementsPolicy;
use App\Utility\PolicyResources\Decaissements as DecaissementsResource;
use App\Policies\Decaissements\DecaissementPolicy;
use App\Models\Decaissement as DecaissementResource;
use App\Policies\OrdreVirementsPolicy;
use App\Utility\PolicyResources\OrdreVirements as OrdreVirementsResource;
use App\Policies\OrdreVirements\OrdreVirementPolicy;
use App\Models\OrdreVirement as OrdreVirementResource;
use App\Policies\TransactionsPolicy;
use App\Utility\PolicyResources\Finances\Indicateurs as IndicateursResource;
use App\Policies\Finances\IndicateursPolicy;
use App\Policies\ComptesBancaires\RecapitulatifPolicy;
use App\Utility\PolicyResources\ComptesBancaires\Recapitulatif as RecapitulatifResource;
use App\Policies\Transactions\TransactionPolicy;
use App\Utility\PolicyResources\Transactions as TransactionsResource;
use App\Models\Transaction as TransactionResource;
use App\Policies\Transactions\TypesPolicy;
use App\Utility\PolicyResources\Transactions\Types as TransactionsTypesResource;
use App\Models\LeveeFond as LeveeFondResource;
use App\Policies\LeveeFondsPolicy;
use App\Utility\PolicyResources\LeveeFonds as LeveeFondsResource;
use App\Utility\PolicyResources\ExercicesFiscaux as ExercicesFiscauxResource;
use App\Policies\ExercicesFiscauxPolicy;
use App\Policies\ComptesBancaires\CompteBancaire\TransactionsPolicy as ComptesBancairesCompteBancaireTransactionsPolicy;
use App\Utility\PolicyResources\ComptesBancaires\CompteBancaire\Transactions as ComptesBancairesCompteBancaireTransactionsResource;
use App\Policies\ExercicesFiscaux\ExerciceFiscalPolicy;
use App\Models\ExerciceFiscal as ExerciceFiscalResource;
use App\Policies\LeveeFonds\LeveeFondPolicy;
use App\Utility\PolicyResources\Finances\Recapitulatif as FinancesRecapitulatifResource;
use App\Policies\Finances\RecapitulatifPolicy as FinancesRecapitulatifPolicy;
use App\Utility\PolicyResources\Finances\Recettes as FinancesRecettesResource;
use App\Policies\Finances\RecettesPolicy as FinancesRecettesPolicy;
use App\Utility\PolicyResources\Finances\Charges as FinancesChargesResource;
use App\Policies\Finances\ChargesPolicy as FinancesChargesPolicy;
use App\Utility\PolicyResources\BusinessPlans\BpHypotheses as BusinessPlansBpHypotheses;
use App\Policies\BusinessPlans\BpHypothesesPolicy as BusinessPlansBpHypothesesPolicy;
use App\Utility\PolicyResources\BusinessPlans\BpInvestissements as BusinessPlansBpInvestissements;
use App\Policies\BusinessPlans\BpInvestissementsPolicy as BusinessPlansBpInvestissementsPolicy;
use App\Utility\PolicyResources\BusinessPlans as BusinessPlans;
use App\Policies\BusinessPlansPolicy as BusinessPlansPolicy;
use App\Policies\Decomptes\Commentaires\CommentairesPolicy;
use App\Utility\PolicyResources\Decomptes\Commentaires\Commentaires as DecomptesCommentairesCommentairesResource;
use App\Policies\DecomptesPartielsPolicy;
use App\Utility\PolicyResources\DecomptesPartiels as DecomptesPartielsResource;
use App\Policies\DecomptesPartiels\DecomptePartielPolicy;
use App\Models\DecomptePartiel as DecomptePartielResource;
use App\Policies\ProjectionTarifsPolicy;
use App\Utility\PolicyResources\ProjectionTarifs as ProjectionTarifsResource;

use App\Utility\PolicyResources\BusinessPlans\InvestissementsType3 as BusinessPlansInvestissementsType3;
use App\Utility\PolicyResources\BusinessPlans\InvestissementsType4 as BusinessPlansInvestissementsType4;
use App\Utility\PolicyResources\BusinessPlans\InvestissementsType5 as BusinessPlansInvestissementsType5;
use App\Utility\PolicyResources\BusinessPlans\InvestissementsType6 as BusinessPlansInvestissementsType6;

use App\Policies\BusinessPlans\InvestissementsTypes3Policy as BusinessPlansInvestissementsTypes3Policy;
use App\Policies\BusinessPlans\InvestissementsTypes4Policy as BusinessPlansInvestissementsTypes4Policy;
use App\Policies\BusinessPlans\InvestissementsTypes5Policy as BusinessPlansInvestissementsTypes5Policy;
use App\Policies\BusinessPlans\InvestissementsTypes6Policy as BusinessPlansInvestissementsTypes6Policy;

use App\Policies\AuditsPolicy;
use App\Utility\PolicyResources\Audits as AuditsResource;

use App\Policies\Audits\AuditPolicy;
use App\Models\Audit as AuditResource;

use App\Policies\ComptesBancairesPolicy;
use App\Utility\PolicyResources\ComptesBancaires as ComptesBancairesResource;

use App\Policies\PlanComptablesPolicy;
use App\Utility\PolicyResources\PlanComptables as PlanComptablesResource;
use App\Models\ProfilEmploye as ProfilEmployeResource;
use App\Policies\ProfilEmployesPolicy;
use App\Utility\PolicyResources\ProfilEmployes as ProfilEmployesResource;
use App\Models\Plan as PlanResource;
use App\Policies\PlansPolicy;
use App\Utility\PolicyResources\Plans as PlansResource;
use App\Models\Permission as PermissionResource;
use App\Policies\PermissionsPolicy;
use App\Utility\PolicyResources\Permissions as PermissionsResource;
use App\Models\Role as RoleResource;
use App\Policies\RolesPolicy;
use App\Utility\PolicyResources\Roles as RolesResource;
use App\Models\Abonnement as AbonnementResource;
use App\Policies\AbonnementsPolicy;
use App\Utility\PolicyResources\Abonnements as AbonnementsResource;
use App\Models\Entreprise as EntrepriseResource;
use App\Policies\EntreprisesPolicy;
use App\Utility\PolicyResources\Entreprises as EntreprisesResource;
use App\Models\Employe as EmployeResource;
use App\Policies\EmployesPolicy;
use App\Utility\PolicyResources\Employes as EmployesResource;
use App\Models\Agrement as AgrementResource;
use App\Policies\AgrementsPolicy;
use App\Utility\PolicyResources\Agrements as AgrementsResource;
use App\Models\Base as BaseResource;
use App\Policies\BasesPolicy;
use App\Utility\PolicyResources\Bases as BasesResource;
use App\Models\Module as ModuleResource;
use App\Policies\ModulesPolicy;
use App\Utility\PolicyResources\Modules as ModulesResource;
use App\Models\RolePermission as RolePermissionResource;
use App\Policies\RolesPermissionsPolicy;
use App\Utility\PolicyResources\RolesPermissions as RolesPermissionsResource;
use App\Models\Fonctionnalite as FonctionnaliteResource;
use App\Policies\FonctionnalitesPolicy;
use App\Utility\PolicyResources\Fonctionnalites as FonctionnalitesResource;
use App\Models\PlanModule as PlanModuleResource;
use App\Policies\PlansModulesPolicy;
use App\Utility\PolicyResources\PlansModules as PlansModulesResource;
use App\Models\Formation as FormationResource;
use App\Policies\FormationsPolicy;
use App\Utility\PolicyResources\Formations as FormationsResource;
use App\Models\EmployeFormation as EmployeFormationResource;
use App\Policies\EmployeFormationsPolicy;
use App\Utility\PolicyResources\EmployeFormations as EmployeFormationsResource;
use App\Models\NominationEmploye as NominationEmployeResource;
use App\Policies\NominationsEmployesPolicy;
use App\Utility\PolicyResources\NominationsEmployes as NominationsEmployesResource;

use App\Models\Avancement as AvancementResource;
use App\Policies\AvancementsPolicy;
use App\Utility\PolicyResources\Avancements as AvancementsResource;

use App\Models\Recurrence as RecurrenceResource;
use App\Policies\RecurrencesPolicy;
use App\Utility\PolicyResources\Recurrences as RecurrencesResource;

use App\Models\Statut as StatutResource;
use App\Policies\StatutsPolicy;
use App\Utility\PolicyResources\Statuts as StatutsResource;

use App\Models\TypeOrigineAction as TypeOrigineActionResource;
use App\Policies\TypeOrigineActionsPolicy;
use App\Utility\PolicyResources\TypeOrigineActions as TypeOrigineActionsResource;

use App\Models\GraviteRisque as GraviteRisqueResource;
use App\Policies\GraviteRisquesPolicy;
use App\Utility\PolicyResources\GraviteRisques as GraviteRisquesResource;

use App\Models\ProbabiliteRisque as ProbabiliteRisqueResource;
use App\Policies\ProbabiliteRisquesPolicy;
use App\Utility\PolicyResources\ProbabiliteRisques as ProbabiliteRisquesResource;

use App\Models\Famille as FamilleResource;
use App\Policies\FamillesPolicy;
use App\Utility\PolicyResources\Familles as FamillesResource;

use App\Models\UtilisateurRole as UtilisateurRoleResource;
use App\Policies\UtilisateursRolesPolicy;
use App\Utility\PolicyResources\UtilisateursRoles as UtilisateursRolesResource;


use App\Policies\SaisieOperartionsPolicy;
use App\Utility\PolicyResources\SaisieOperartions as SaisieOperartionsResource;

use App\Policies\ImporterPolicy;
use App\Utility\PolicyResources\Importer as ImporterResource;

use App\Policies\Marches\AvenantsPolicy as MarchesAvenantsPolicy;
use App\Utility\PolicyResources\Avenants as AvenantsResource;

use App\Policies\Marches\Avenant\AvenantPolicy as MarchesAvenantsAvenantPolicy;
use App\Models\Avenant as AvenantResource;

use App\Policies\Prestataires\PrestatairePolicy;
use App\Models\Prestataire as PrestataireResource;

use App\Policies\Marches\AccordsFinancementsPolicy as MarchesAccordsFinancementsPolicy;
use App\Utility\PolicyResources\AccordsFinancements as AccordsFinancementsResource;

use App\Policies\Marches\AccordFinancement\AccordFinancementPolicy as MarchesAccordsFinancementAccordFinancementPolicy;
use App\Models\AccordFinancement as AccordFinancementResource;

use App\Policies\MissionControlsPolicy as MissionControlsPolicy;
use App\Utility\PolicyResources\MissionControls as MissionControlsResource;

use App\Policies\MissionControls\MissionControlPolicy as MissionControlPolicy;
use App\Models\MissionControle as MissionControlResource;

use App\Policies\BanquesPolicy as BanquesPolicy;
use App\Utility\PolicyResources\Banques as BanquesResource;

use App\Policies\ExecutionsBudgetairesPolicy as ExecutionsBudgetairesPolicy;
use App\Utility\PolicyResources\ExecutionsBudgetaires as ExecutionsBudgetairesResource;

use App\Policies\JournauxPolicy as JournauxPolicy;
use App\Utility\PolicyResources\Journaux as JournauxResource;

use App\Policies\BilanComptablesPolicy as BilanComptablesPolicy;
use App\Utility\PolicyResources\BilanComptable as BilanComptableResource;

use App\Policies\Banques\BanquePolicy as BanquePolicy;
use App\Models\Banque as BanqueResource;

use App\Policies\ComptesBancaires\CompteBancairePolicy as CompteBancairePolicy;
use App\Models\CompteBancaire as CompteBancaireResource;
use App\Policies\ComptabiliteGlobalePolicy as ComptabiliteGlobalePolicy;
use App\Utility\PolicyResources\ComptabiliteGlobales as ComptabiliteGlobalesResource;

use App\Policies\ChargesPolicy as ChargesPolicyPolicy;
use App\Utility\PolicyResources\Charges as ChargesResource;

use App\Policies\PlanComptesPolicy as PlanComptesPolicy;
use App\Utility\PolicyResources\PlanComptes as PlanComptesResource;

use App\Policies\PlanComptes\PlanComptePolicy as PlanComptePolicy;
use App\Models\PlanCompte as PlanCompteResource;

use App\Policies\ExecutionBudgetaires\ExecutionBudgetairePolicy as ExecutionBudgetairePolicy;
use App\Models\ComptabiliteGlobale as ExecutionBudgetaireResource;
 

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        //'App\Models\Model' => 'App\Policies\ModelPolicy',
        ExecutionBudgetaireResource::class => ExecutionBudgetairePolicy::class,
        ProfilEmployeResource::class => ProfilEmployesPolicy::class,
        PlansResource::class => PlansPolicy::class,
        PlanResource::class => PlansPolicy::class,
        PermissionsResource::class => PermissionsPolicy::class,
        PermissionResource::class => PermissionsPolicy::class,
        RolesResource::class => RolesPolicy::class,
        RoleResource::class => RolesPolicy::class,
        AbonnementsResource::class => AbonnementsPolicy::class,
        AbonnementResource::class => AbonnementsPolicy::class,
        EntreprisesResource::class => EntreprisesPolicy::class,
        EntrepriseResource::class => EntreprisesPolicy::class,
        EmployesResource::class => EmployesPolicy::class,
        EmployeResource::class => EmployesPolicy::class,
        AgrementsResource::class => AgrementsPolicy::class,
        AgrementResource::class => AgrementsPolicy::class,
        BasesResource::class => BasesPolicy::class,
        BaseResource::class => BasesPolicy::class,
        ModulesResource::class => ModulesPolicy::class,
        ModuleResource::class => ModulesPolicy::class,
        RolesPermissionsResource::class => RolesPermissionsPolicy::class,
        RolePermissionResource::class => RolesPermissionsPolicy::class,
        FonctionnalitesResource::class => FonctionnalitesPolicy::class,
        FonctionnaliteResource::class => FonctionnalitesPolicy::class,
        PlansModulesResource::class => PlansModulesPolicy::class,
        PlanModuleResource::class => PlansModulesPolicy::class,
        FormationsResource::class => FormationsPolicy::class,
        FormationResource::class => FormationsPolicy::class,
        EmployeFormationsResource::class => EmployeFormationsPolicy::class,
        EmployeFormationResource::class => EmployeFormationsPolicy::class,
        NominationsEmployesResource::class => NominationsEmployesPolicy::class,
        NominationEmployeResource::class => NominationsEmployesPolicy::class,
        AuthentifierResource::class => AuthentifierPolicy::class,
        DeconnecterResource::class => DeconnecterPolicy::class,
        ReinitialiserMotDePasseResource::class => ReinitialiserMotDePassePolicy::class,
        UtilisateursResource::class => UtilisateursPolicy::class,
        UtilisateurResource::class => UtilisateurPolicy::class,
        AvancementsResource::class => AvancementsPolicy::class,
        AvancementResource::class => AvancementsPolicy::class,
        RecurrencesResource::class => RecurrencesPolicy::class,
        RecurrenceResource::class => RecurrencesPolicy::class,
        StatutsResource::class => StatutsPolicy::class,
        StatutResource::class => StatutsPolicy::class,
        TypeOrigineActionsResource::class => TypeOrigineActionsPolicy::class,
        TypeOrigineActionResource::class => TypeOrigineActionsPolicy::class,
        GraviteRisquesResource::class => GraviteRisquesPolicy::class,
        GraviteRisqueResource::class => GraviteRisquesPolicy::class,
        ProbabiliteRisquesResource::class => ProbabiliteRisquesPolicy::class,
        ProbabiliteRisqueResource::class => ProbabiliteRisquesPolicy::class,
        FamillesResource::class => FamillesPolicy::class,
        FamilleResource::class => FamillesPolicy::class,
        UtilisateursRolesResource::class => UtilisateursRolesPolicy::class,
        UtilisateurRoleResource::class => UtilisateursRolesPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::before(function (?UtilisateurResource $user, $ability, $arguments) {
            if (is_null($user)) {
                return null;
            }

            $resourceClass = null;
            if (is_array($arguments) && count($arguments) > 0) {
                $first = $arguments[0];
                if (is_string($first) && class_exists($first)) {
                    $resourceClass = $first;
                } elseif (is_object($first)) {
                    $resourceClass = get_class($first);
                }
            }

            $permissionLabel = $this->resolvePermissionLabel($ability, $resourceClass);
            if ($permissionLabel && $user->hasPermission($permissionLabel)) {
                return true;
            }

            return null;
        });

        Passport::routes();
        Passport::tokensExpireIn(now()->addSeconds(3600*24));
        Passport::refreshTokensExpireIn(now()->addSeconds(3600*25));
    }

    private function resolvePermissionLabel(string $ability, ?string $resourceClass): ?string
    {
        if (is_null($resourceClass)) {
            return null;
        }

        $resourceName = class_basename($resourceClass);
        $snakeResource = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $resourceName));

        return $snakeResource . '.' . $ability;
    }
}

