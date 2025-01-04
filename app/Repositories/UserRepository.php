<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function getAll()
    {
        return User::all();
    }

    public function find($user_id)
    {
        return User::findOrFail($user_id);
    }

    public function create(array $data)
    {
        return User::create($data);
    }

    public function update($user_id, array $data)
    {
        $user = User::findOrFail($user_id);
        $user->update($data);

        return $user;  // Return the updated user
    }

    public function delete($user_id)
    {
        $user = User::findOrFail($user_id);

        return $user->delete();
    }
}
