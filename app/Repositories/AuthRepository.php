<?php

namespace App\Repositories;

use App\Models\User;

class AuthRepository
{
    public function create(array $data)
    {
        // save roles and permisions as json
        $data['roles_and_permissions'] = json_encode((object)[
            "admin" => (object)['read' => true, 'create' => true, 'update' => false, 'delete' => false],
        ]);

        return User::create($data);
    }

    public function find($user_id)
    {
        return User::findOrFail($user_id);
    }
}
