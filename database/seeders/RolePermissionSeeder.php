<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $collection = collect([
            'Admin',
            'Ads',
            'Agent',
            'CashOut',
            'CashOutMethod',
            'ContactUs',
            'Faq',
            'GameRoom',
            'GameServer',
            'GameTransaction',
            'Games',
            'Ledger',
            'Notification',
            'PaymentAccount',
            'PaymentMethod',
            'PrivacyPolicy',
            'Programs',
            'Recommendation',
            'Result',
            'Section',
            'TermsAndConditions', 
            'TopUp',
            'UserGameWallet',
            'UserMainWallet',
            'PayBack',
            'Tutorial',
            'TwoDCloseDay',
            'TwoDHistory',
            'User',
            'BanList',
            'Winner',
            'MmAfterNoonBet',
            'MmAfterNoonNumber',
            'MmEveningBet',
            'MmEveningNumber',
            'MmMorningBet',
            'MmMorningNumber',
            'MmNoonBet',
            'MmNoonNumber',
            'ThreDNumber',
            'ThreDNumberBet',
            'ThreeDHistory',
            'ThreeDLedger',
            'ThreeDPayBack',
            'ThreeDResult',
            'ThreeDWinner',
            'ThreeDVoucher',
            'Role',
            'Voucher',
            'Permission'
            
            // ... your own models/permission you want to crate
        ]);

        $collection->each(function ($item, $key) {
            // create permissions for each collection item
            Permission::create(['group' => $item, 'name' => 'viewAny' . $item]);
            Permission::create(['group' => $item, 'name' => 'view' . $item]);
            Permission::create(['group' => $item, 'name' => 'update' . $item]);
            Permission::create(['group' => $item, 'name' => 'create' . $item]);
            Permission::create(['group' => $item, 'name' => 'delete' . $item]);
            Permission::create(['group' => $item, 'name' => 'destroy' . $item]);
        });

        // Create a Super-Admin Role and assign all permissions to it
        $role = Role::create(['name' => 'super-admin']);
        $role->givePermissionTo(Permission::all());

        // Give User Super-Admin Role
        $user = \App\Models\Admin::whereEmail('info@kohtut.me')->first(); // enter your email here
        $user->assignRole('super-admin');
    }
}