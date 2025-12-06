<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useSoftDeletes   = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;

    protected $allowedFields = [
        'username',
        'password_hash',
        'full_name',
        'email',
        'role_id',
        'active',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Cari user aktif berdasarkan username.
     */
    public function findByUsername(string $username): ?array
    {
        return $this
            ->where('username', $username)
            ->where('active', 1)
            ->first();
    }
}
