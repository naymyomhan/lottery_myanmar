<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Sereny\NovaPermissions\Policies\BasePolicy;

class MmAfterNoonBetPolicy extends BasePolicy
{
     use HandlesAuthorization;

    public $key = 'MmAfterNoonBet';

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