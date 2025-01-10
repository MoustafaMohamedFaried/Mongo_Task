<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function create(array $data)
    {
        return User::create($data);
    }

    public function find($user_id)
    {
        return User::findOrFail($user_id);
    }
}
