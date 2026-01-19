<?php

namespace App\Services;

use App\Models\AuditLogModel;

/**
 * AuditLogService — Centralized audit logging for all modules.
 *
 * Usage:
 *   $auditService = new AuditLogService();
 *   $auditService->log('customer', 'create', $customerId, $payload, 'Customer created');
 *
 * Security events:
 *   $auditService->logAuth('login_success', $userId, ['username' => $username]);
 *
 * Features:
 * - Auto-fills user_id from session (if available)
 * - Auto-fills created_at
 * - Safe JSON encoding (UNESCAPED_UNICODE | UNESCAPED_SLASHES)
 * - Filters sensitive fields (password_hash, password, token, etc.)
 */
class AuditLogService
{
    protected AuditLogModel $model;

    /**
     * Fields to always strip from payload to avoid logging sensitive data.
     */
    protected array $sensitiveFields = [
        'password',
        'password_hash',
        'password_confirm',
        'token',
        'csrf_token',
        'csrf_test_name',
    ];

    public function __construct()
    {
        $this->model = new AuditLogModel();
    }

    /**
     * Log an audit event.
     *
     * @param string          $entityType  Module/entity name (e.g., 'customer', 'sale', 'auth')
     * @param string          $action      Action performed (e.g., 'create', 'update', 'delete', 'void', 'login_success')
     * @param int|null        $entityId    ID of the affected record (null for auth events or bulk ops)
     * @param array|string|null $payload   Full snapshot data (array preferred, will be JSON-encoded)
     * @param string|null     $description Human-readable description
     * @param array           $meta        Optional extra context (not stored in DB yet, reserved for future)
     * @return int|false      Insert ID or false on failure
     */
    public function log(
        string $entityType,
        string $action,
        ?int $entityId = null,
        array|string|null $payload = null,
        ?string $description = null,
        array $meta = []
    ): int|false {
        // Sanitize payload
        $payloadJson = $this->preparePayload($payload);

        // Get current user from session (may be null for public/auth routes)
        $userId = $this->getCurrentUserId();

        $data = [
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'action'      => $action,
            'description' => $description,
            'payload'     => $payloadJson,
            'user_id'     => $userId,
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        $result = $this->model->insert($data);

        return $result ? (int) $this->model->getInsertID() : false;
    }

    /**
     * Shortcut for logging security/auth events.
     *
     * @param string          $action      e.g., 'login_success', 'login_fail', 'logout', 'forgot_request', 'reset_success'
     * @param int|null        $userId      User ID involved (may be null for failed login with unknown user)
     * @param array|null      $payload     Additional context (username, email, ip, etc.)
     * @param string|null     $description Human-readable note
     * @return int|false
     */
    public function logAuth(
        string $action,
        ?int $userId = null,
        ?array $payload = null,
        ?string $description = null
    ): int|false {
        return $this->log('auth', $action, $userId, $payload, $description);
    }

    /**
     * Prepare payload for storage: filter sensitive fields and encode to JSON.
     */
    protected function preparePayload(array|string|null $payload): ?string
    {
        if ($payload === null) {
            return null;
        }

        if (is_string($payload)) {
            // Already JSON string — validate and return
            $decoded = json_decode($payload, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $payload = $this->filterSensitive($decoded);
                return json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
            // Invalid JSON string, store as-is (edge case)
            return $payload;
        }

        // Array payload
        $payload = $this->filterSensitive($payload);
        return json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Recursively remove sensitive fields from payload.
     */
    protected function filterSensitive(array $data): array
    {
        foreach ($this->sensitiveFields as $field) {
            if (array_key_exists($field, $data)) {
                unset($data[$field]);
            }
        }

        // Recurse into nested arrays
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->filterSensitive($value);
            }
        }

        return $data;
    }

    /**
     * Get current user ID from session (null if not logged in).
     */
    protected function getCurrentUserId(): ?int
    {
        $userId = session('user_id');
        if ($userId === null || $userId === '' || $userId === 0) {
            return null;
        }
        return (int) $userId;
    }

    /**
     * Get request metadata for enhanced logging (IP, user agent, URI).
     * Reserved for future use if schema is extended.
     */
    public function getRequestMeta(): array
    {
        $request = service('request');
        return [
            'ip'         => $request->getIPAddress(),
            'user_agent' => $request->getUserAgent()->getAgentString(),
            'uri'        => (string) $request->getUri(),
            'method'     => $request->getMethod(),
        ];
    }
}
