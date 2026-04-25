<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Channel;
use App\Models\Contract;
use App\Models\DynamicEntry;
use App\Models\DynamicModule;
use App\Models\Employee;
use App\Models\FinanceRecord;
use App\Models\Folder;
use App\Models\Message;
use App\Models\Organization;
use App\Models\PaymentMethod;
use App\Models\Project;
use App\Models\Suggestion;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Super Admin ──────────────────────────────────────────
        $this->command->info('Creating Super Admin...');

        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@rdt.com',
            'password' => Hash::make('password'),
            'role' => 'SUPER_ADMIN',
            'organization_id' => null,
        ]);

        // ── 2. Organization ─────────────────────────────────────────
        $this->command->info('Creating Organization...');

        $org = Organization::create([
            'name' => 'Kantox Technologies',
            'domain' => 'kantox.bi',
            'address' => 'Bujumbura, Burundi',
            'phone' => '+257 79 000 000',
            'email' => 'info@kantox.bi',
            'nif' => '4000/0001',
            'plan' => 'premium',
            'monthly_fee' => 50000,
            'status' => 'active',
            'modules' => [
                'employees', 'tasks', 'projects', 'finance',
                'archives', 'communication', 'builder', 'attendance',
            ],
        ]);

        // ── 3. Admin User ───────────────────────────────────────────
        $this->command->info('Creating Admin User...');

        $adminUser = User::create([
            'name' => 'Alice Mensah',
            'email' => 'admin@kantox.bi',
            'password' => Hash::make('password'),
            'role' => 'ORG_ADMIN',
            'organization_id' => $org->id,
        ]);

        // ── 4. Employee User ────────────────────────────────────────
        $this->command->info('Creating Employee User...');

        $employeeUser = User::create([
            'name' => 'Sarah Lawson',
            'email' => 'employee@kantox.bi',
            'password' => Hash::make('password'),
            'role' => 'EMPLOYEE',
            'organization_id' => $org->id,
        ]);

        // ── 5. Employees ────────────────────────────────────────────
        $this->command->info('Creating Employees...');

        $sarah = Employee::create([
            'organization_id' => $org->id,
            'user_id' => $employeeUser->id,
            'name' => 'Sarah Lawson',
            'email' => 'sarah.lawson@kantox.bi',
            'phone' => '+257 79 100 001',
            'role' => 'Employé',
            'department' => 'RH',
            'status' => 'Active',
            'joined_at' => '2024-03-15',
        ]);

        $marc = Employee::create([
            'organization_id' => $org->id,
            'user_id' => null,
            'name' => 'Marc Kouassi',
            'email' => 'marc.kouassi@kantox.bi',
            'phone' => '+257 79 100 002',
            'role' => 'Leader',
            'department' => 'IT',
            'status' => 'Active',
            'joined_at' => '2023-06-01',
        ]);

        $jean = Employee::create([
            'organization_id' => $org->id,
            'user_id' => null,
            'name' => 'Jean Dupont',
            'email' => 'jean.dupont@kantox.bi',
            'phone' => '+257 79 100 003',
            'role' => 'Directeur Général',
            'department' => 'Direction',
            'status' => 'Active',
            'joined_at' => '2022-01-10',
        ]);

        $paul = Employee::create([
            'organization_id' => $org->id,
            'user_id' => null,
            'name' => 'Paul Atreides',
            'email' => 'paul.atreides@kantox.bi',
            'phone' => '+257 79 100 004',
            'role' => 'Employé',
            'department' => 'Finance',
            'status' => 'Inactive',
            'joined_at' => '2023-11-20',
        ]);

        $fatima = Employee::create([
            'organization_id' => $org->id,
            'user_id' => null,
            'name' => 'Fatima Ndiaye',
            'email' => 'fatima.ndiaye@kantox.bi',
            'phone' => '+257 79 100 005',
            'role' => 'Leader',
            'department' => 'Operations',
            'status' => 'Active',
            'joined_at' => '2024-01-05',
        ]);

        // ── 6. Projects ─────────────────────────────────────────────
        $this->command->info('Creating Projects...');

        $projectAlpha = Project::create([
            'organization_id' => $org->id,
            'name' => 'Projet Alpha',
            'description' => 'Refonte complète du système de gestion interne.',
            'category' => 'IT',
            'status' => 'En cours',
            'progress' => 45,
            'budget' => 15000000,
            'deadline' => '2026-09-30',
            'team' => ['Marc Kouassi', 'Sarah Lawson'],
        ]);

        $projectBeta = Project::create([
            'organization_id' => $org->id,
            'name' => 'Expansion Régionale',
            'description' => 'Ouverture de nouveaux bureaux en Afrique de l\'Est.',
            'category' => 'Business',
            'status' => 'Planifié',
            'progress' => 10,
            'budget' => 50000000,
            'deadline' => '2027-03-15',
            'team' => ['Jean Dupont', 'Fatima Ndiaye'],
        ]);

        $projectGamma = Project::create([
            'organization_id' => $org->id,
            'name' => 'Formation Continue',
            'description' => 'Programme de formation pour tous les employés.',
            'category' => 'RH',
            'status' => 'Terminé',
            'progress' => 100,
            'budget' => 3000000,
            'deadline' => '2026-02-28',
            'team' => ['Sarah Lawson'],
        ]);

        $projectDelta = Project::create([
            'organization_id' => $org->id,
            'name' => 'Audit Financier 2026',
            'description' => 'Audit annuel des comptes et conformité.',
            'category' => 'Finance',
            'status' => 'En cours',
            'progress' => 60,
            'budget' => 5000000,
            'deadline' => '2026-06-30',
            'team' => ['Paul Atreides', 'Jean Dupont'],
        ]);

        // ── 7. Tasks ────────────────────────────────────────────────
        $this->command->info('Creating Tasks...');

        $task1 = Task::create([
            'organization_id' => $org->id,
            'project_id' => $projectAlpha->id,
            'title' => 'Maquettes UI/UX',
            'description' => 'Créer les maquettes pour le nouveau tableau de bord.',
            'status' => 'COMPLETED',
            'priority' => 'HIGH',
            'progress' => 100,
            'due_date' => '2026-05-15',
        ]);

        $task2 = Task::create([
            'organization_id' => $org->id,
            'project_id' => $projectAlpha->id,
            'title' => 'API Authentification',
            'description' => 'Implémenter le système d\'authentification JWT.',
            'status' => 'IN_PROGRESS',
            'priority' => 'CRITICAL',
            'progress' => 70,
            'due_date' => '2026-05-30',
        ]);

        $task3 = Task::create([
            'organization_id' => $org->id,
            'project_id' => $projectBeta->id,
            'title' => 'Étude de marché',
            'description' => 'Analyser les opportunités dans la région Est.',
            'status' => 'PENDING',
            'priority' => 'MEDIUM',
            'progress' => 0,
            'due_date' => '2026-07-01',
        ]);

        $task4 = Task::create([
            'organization_id' => $org->id,
            'project_id' => $projectDelta->id,
            'title' => 'Rapport trimestriel Q1',
            'description' => 'Préparer le rapport financier du premier trimestre.',
            'status' => 'CANCELLED',
            'priority' => 'LOW',
            'progress' => 20,
            'due_date' => '2026-04-15',
        ]);

        $task5 = Task::create([
            'organization_id' => $org->id,
            'project_id' => $projectGamma->id,
            'title' => 'Module sécurité informatique',
            'description' => 'Préparer le contenu de la formation cybersécurité.',
            'status' => 'IN_PROGRESS',
            'priority' => 'HIGH',
            'progress' => 50,
            'due_date' => '2026-06-10',
        ]);

        $task1->assignees()->attach([$marc->id, $sarah->id]);
        $task2->assignees()->attach([$marc->id]);
        $task3->assignees()->attach([$jean->id, $fatima->id]);
        $task4->assignees()->attach([$paul->id]);
        $task5->assignees()->attach([$sarah->id, $fatima->id]);

        // ── 8. Finance Records ──────────────────────────────────────
        $this->command->info('Creating Finance Records...');

        FinanceRecord::create([
            'organization_id' => $org->id,
            'date' => '2026-04-01',
            'description' => 'Contrat client MegaCorp',
            'type' => 'Revenu',
            'montant' => 8500000,
            'statut' => 'Validé',
        ]);

        FinanceRecord::create([
            'organization_id' => $org->id,
            'date' => '2026-04-05',
            'description' => 'Salaires Avril 2026',
            'type' => 'Dépense',
            'montant' => 12000000,
            'statut' => 'Validé',
        ]);

        FinanceRecord::create([
            'organization_id' => $org->id,
            'date' => '2026-04-10',
            'description' => 'Vente de licences logicielles',
            'type' => 'Revenu',
            'montant' => 3200000,
            'statut' => 'En attente',
        ]);

        FinanceRecord::create([
            'organization_id' => $org->id,
            'date' => '2026-04-18',
            'description' => 'Achat matériel informatique',
            'type' => 'Dépense',
            'montant' => 4500000,
            'statut' => 'Validé',
        ]);

        FinanceRecord::create([
            'organization_id' => $org->id,
            'date' => '2026-04-25',
            'description' => 'Prestation consulting externe',
            'type' => 'Dépense',
            'montant' => 1800000,
            'statut' => 'En attente',
        ]);

        // ── 9. Attendance Records ───────────────────────────────────
        $this->command->info('Creating Attendance Records...');

        $today = now()->toDateString();

        Attendance::create([
            'organization_id' => $org->id,
            'employee_id' => $sarah->id,
            'date' => $today,
            'arrivee' => '07:55',
            'depart' => '17:00',
            'statut' => 'Présent',
            'poste' => 'RH',
        ]);

        Attendance::create([
            'organization_id' => $org->id,
            'employee_id' => $marc->id,
            'date' => $today,
            'arrivee' => '08:30',
            'depart' => '17:00',
            'statut' => 'Retard',
            'poste' => 'IT',
        ]);

        Attendance::create([
            'organization_id' => $org->id,
            'employee_id' => $jean->id,
            'date' => $today,
            'arrivee' => '07:45',
            'depart' => '17:00',
            'statut' => 'Présent',
            'poste' => 'Direction',
        ]);

        Attendance::create([
            'organization_id' => $org->id,
            'employee_id' => $fatima->id,
            'date' => $today,
            'arrivee' => null,
            'depart' => null,
            'statut' => 'Absent',
            'poste' => 'Operations',
        ]);

        // ── 10. Channels & Messages ─────────────────────────────────
        $this->command->info('Creating Channels & Messages...');

        $general = Channel::create([
            'organization_id' => $org->id,
            'name' => 'Général',
            'type' => 'PUBLIC',
        ]);

        $annonces = Channel::create([
            'organization_id' => $org->id,
            'name' => 'Annonces',
            'type' => 'ANNOUNCEMENT',
        ]);

        $itTeam = Channel::create([
            'organization_id' => $org->id,
            'name' => 'IT Team',
            'type' => 'PRIVATE',
        ]);

        Message::create(['channel_id' => $general->id, 'user_id' => $adminUser->id, 'text' => 'Bienvenue sur le canal général !']);
        Message::create(['channel_id' => $general->id, 'user_id' => $employeeUser->id, 'text' => 'Merci ! Ravie d\'être ici.']);
        Message::create(['channel_id' => $general->id, 'user_id' => $adminUser->id, 'text' => 'N\'hésitez pas à poser vos questions.']);

        Message::create(['channel_id' => $annonces->id, 'user_id' => $adminUser->id, 'text' => 'Réunion plénière vendredi à 10h.']);
        Message::create(['channel_id' => $annonces->id, 'user_id' => $adminUser->id, 'text' => 'Mise à jour de la politique de congés disponible sur l\'intranet.']);

        Message::create(['channel_id' => $itTeam->id, 'user_id' => $adminUser->id, 'text' => 'Déploiement prévu ce soir à 22h.']);
        Message::create(['channel_id' => $itTeam->id, 'user_id' => $employeeUser->id, 'text' => 'Bien noté, je prépare le rollback plan.']);

        // ── 11. Suggestions ─────────────────────────────────────────
        $this->command->info('Creating Suggestions...');

        Suggestion::create([
            'organization_id' => $org->id,
            'user_id' => $employeeUser->id,
            'text' => 'Ajouter un mode sombre à l\'application pour un meilleur confort visuel.',
            'votes' => 12,
            'status' => 'open',
        ]);

        Suggestion::create([
            'organization_id' => $org->id,
            'user_id' => $adminUser->id,
            'text' => 'Mettre en place un système de notification push pour les échéances.',
            'votes' => 7,
            'status' => 'open',
        ]);

        // ── 12. Folders ─────────────────────────────────────────────
        $this->command->info('Creating Folders...');

        Folder::create([
            'organization_id' => $org->id,
            'name' => 'Documents RH',
            'created_by' => $adminUser->id,
        ]);

        Folder::create([
            'organization_id' => $org->id,
            'name' => 'Rapports Financiers',
            'created_by' => $adminUser->id,
        ]);

        // ── 13. Dynamic Module ──────────────────────────────────────
        $this->command->info('Creating Dynamic Module...');

        $module = DynamicModule::create([
            'organization_id' => $org->id,
            'name' => 'Inventaire Matériel',
            'description' => 'Suivi du matériel informatique de l\'entreprise.',
            'icon' => 'monitor',
            'show_in_sidebar' => true,
            'fields' => [
                ['name' => 'designation', 'type' => 'text', 'label' => 'Désignation', 'required' => true],
                ['name' => 'serial_number', 'type' => 'text', 'label' => 'Numéro de série', 'required' => true],
            ],
        ]);

        DynamicEntry::create([
            'dynamic_module_id' => $module->id,
            'data' => ['designation' => 'MacBook Pro 16"', 'serial_number' => 'MBP-2026-001'],
            'submitted_by' => $adminUser->id,
        ]);

        DynamicEntry::create([
            'dynamic_module_id' => $module->id,
            'data' => ['designation' => 'Écran Dell 27"', 'serial_number' => 'DLL-2025-042'],
            'submitted_by' => $adminUser->id,
        ]);

        // ── 14. Payment Method ──────────────────────────────────────
        $this->command->info('Creating Payment Method...');

        PaymentMethod::create([
            'bank_name' => 'Bancobu',
            'account_number' => '20001-00045-00123456789-42',
            'account_holder' => 'Kantox Technologies SARL',
            'type' => 'BIF',
        ]);

        // ── 15. Contract ────────────────────────────────────────────
        $this->command->info('Creating Contract...');

        Contract::create([
            'organization_id' => $org->id,
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'monthly_fee' => 50000,
            'status' => 'active',
        ]);

        $this->command->info('Database seeding completed successfully!');
    }
}
