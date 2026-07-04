<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Role::firstOrCreate(['name' => 'Admin']);
        $permissions = [
            'make picks',
            'create season',
            'edit season',
            'enter results',
            'use admin',
            'edit settings',
            'manage users',
            'update picks',
            'update results'
        ];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $users = [
            ['name' => 'Clive', 'email' => 'cw@elfstar.co.uk', 'password' => 'R4id3rNat1on'],
            ['name' => 'Jim', 'email' => 'chicagojab@yahoo.com', 'password' => 'ilovethedolphins']
        ];
        foreach ($users as $user) {
            $thisUser = User::where('email', $user['email'])->first();
            if (!$thisUser) {
                $thisUser = User::create([
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'password' => Hash::make($user['password']),
                    'email_verified_at' => NOW()
                ]);
            }

            $roles = $permissions = [];
            switch ($user['name']) {
                case 'Clive':
                    $roles = ['admin'];
                    $permissions = [
                        'make picks',
                        'create season',
                        'edit season',
                        'enter results',
                        'use admin',
                        'edit settings',
                        'manage users',
                        'update picks',
                        'update results'
                    ];
                    break;
                case 'Jim':
                    $permissions = [
                        'make picks'
                    ];
                    break;
                default:
                    break;
            }


            foreach ($roles as $role) {
                $thisUser->assignRole($role);
            }
            $thisUser->givePermissionTo(
                $permissions
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("SET FOREIGN_KEY_CHECKS = 0");
        DB::statement("truncate `users`");
        DB::statement("truncate `roles`");
        DB::statement("truncate `permissions`");
        DB::statement("truncate `model_has_roles`");
        DB::statement("truncate `role_has_permissions`");
        DB::statement("truncate `model_has_permissions`");
        DB::statement("SET FOREIGN_KEY_CHECKS = 1");
    }
};
