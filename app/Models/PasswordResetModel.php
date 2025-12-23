<?php

namespace App\Models;

use CodeIgniter\Model;

class PasswordResetModel extends Model
{
    protected $table         = 'password_resets';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps  = false;

    protected $allowedFields = [
        'user_id',
        'token_hash',
        'expires_at',
        'used_at',
        'created_at',
        'request_ip',
        'user_agent',
    ];

    /**
     * Simpan token reset baru untuk user tertentu.
     */
    public function createTokenForUser(int $userId, string $rawToken, string $expiresAt, ?string $ip, ?string $ua): int
    {
        $hash = $this->hashToken($rawToken);
        $now  = date('Y-m-d H:i:s');

        return (int) $this->insert([
            'user_id'    => $userId,
            'token_hash' => $hash,
            'expires_at' => $expiresAt,
            'created_at' => $now,
            'request_ip' => $ip,
            'user_agent' => $ua,
        ], true);
    }

    /**
     * Cari token yang masih valid (belum dipakai dan belum kedaluwarsa).
     */
    public function findValidReset(int $userId, string $rawToken): ?array
    {
        $now = date('Y-m-d H:i:s');

        $resets = $this->where('user_id', $userId)
            ->where('used_at', null)
            ->where('expires_at >=', $now)
            ->orderBy('created_at', 'DESC')
            ->findAll(5); // ambil beberapa teratas untuk dibandingkan

        $needle = $this->hashToken($rawToken);

        foreach ($resets as $reset) {
            if (! empty($reset['token_hash']) && hash_equals($reset['token_hash'], $needle)) {
                return $reset;
            }
        }

        return null;
    }

    private function hashToken(string $rawToken): string
    {
        return hash('sha256', $rawToken);
    }
}
