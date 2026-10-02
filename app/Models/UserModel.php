<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['username', 'password_hash', 'nama', 'role', 'status', 'created_at'];
    protected $useTimestamps    = false;

    protected $hashPassword = true;

    protected function hashPassword($value): string
    {
        return password_hash($value, PASSWORD_DEFAULT);
    }

    /**
     * Verifikasi kredensial. Password di-rehash otomatis bila algoritma usang.
     */
    public function attempt(string $username, string $password): ?array
    {
        $user = $this->where('username', $username)->first();

        if ($user === null) {
            // Hash dummy: keeps response time similar whether or not the user exists.
            password_verify($password, '$2y$12$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG');

            return null;
        }

        if ($user['status'] !== 'aktif') {
            return null;
        }

        if (! password_verify($password, $user['password_hash'])) {
            return null;
        }

        if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
            $this->update($user['id'], ['password_hash' => $password]);
        }

        return $user;
    }
}
