<?php

namespace Database\Seeders;

use Botble\ACL\Models\User;
use Botble\ACL\Repositories\Interfaces\ActivationInterface;
use Botble\Base\Supports\BaseSeeder;
use Schema;

class UserSeeder extends BaseSeeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        User::truncate();

        $user = new User();
        $user->first_name = 'System';
        $user->last_name = 'Admin';
        $user->email = 'admin@botble.com';
        $user->username = 'botble';
        $user->password = bcrypt('159357');
        $user->super_user = 1;
        $user->manage_supers = 1;
        $user->save();

        event('acl.activating', $user);

        $activationRepository = app(ActivationInterface::class);

        $activation = $activationRepository->createUser($user);

        event('acl.activated', [$user, $activation]);

        $activationRepository->complete($user, $activation->code);
    }
}
