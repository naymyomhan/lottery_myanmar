<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Sereny\NovaPermissions\Policies\BasePolicy;

class UserMainWalletPolicy extends BasePolicy
{
     use HandlesAuthorization;

    public $key = 'UserMainWallet';

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
}