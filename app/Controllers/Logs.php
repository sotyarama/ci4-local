<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;

/**
 * Logs Controller
 *
 * User-facing activity log viewer. Reads from audit_logs table (read-only).
 * This is separate from the legacy AuditLogs controller which remains untouched.
 *
 * Purpose: Curated, readable activity timeline for end users.
 */
class Logs extends BaseController
{
    protected AuditLogModel $auditLogModel;

    public function __construct()
    {
        $this->auditLogModel = new AuditLogModel();
    }

    /**
     * Display activity logs with filters.
     */
    public function index()
    {
        // Get filter parameters
        $entityType = $this->request->getGet('entity_type') ?: null;
        $action     = $this->request->getGet('action') ?: null;
        $dateFrom   = $this->request->getGet('date_from') ?: null;
        $dateTo     = $this->request->getGet('date_to') ?: null;
        $username   = trim((string) ($this->request->getGet('username') ?? ''));

        // Build query - join with users to get username
        $builder = $this->auditLogModel
            ->select('audit_logs.id, audit_logs.entity_type, audit_logs.entity_id, audit_logs.action, audit_logs.description, audit_logs.user_id, audit_logs.created_at, users.username AS user_name')
            ->join('users', 'users.id = audit_logs.user_id', 'left')
            ->orderBy('audit_logs.created_at', 'DESC')
            ->orderBy('audit_logs.id', 'DESC');

        // Apply filters
        if ($entityType) {
            $builder->where('audit_logs.entity_type', $entityType);
        }
        if ($action) {
            $builder->where('audit_logs.action', $action);
        }
        if ($dateFrom) {
            $builder->where('DATE(audit_logs.created_at) >=', $dateFrom);
        }
        if ($dateTo) {
            $builder->where('DATE(audit_logs.created_at) <=', $dateTo);
        }
        if ($username !== '') {
            $builder->like('users.username', $username, 'both');
        }

        // Limit results for performance
        $logs = $builder->findAll(200);

        // Get distinct entity types for filter dropdown
        $entityTypes = $this->auditLogModel
            ->select('entity_type')
            ->distinct()
            ->orderBy('entity_type', 'ASC')
            ->findAll();
        $entityTypeList = array_column($entityTypes, 'entity_type');

        // Get distinct actions for filter dropdown
        $actions = $this->auditLogModel
            ->select('action')
            ->distinct()
            ->orderBy('action', 'ASC')
            ->findAll();
        $actionList = array_column($actions, 'action');

        // Format entity type labels for display
        $entityTypeLabels = $this->getEntityTypeLabels();
        $actionLabels     = $this->getActionLabels();

        $data = [
            'title'            => 'Activity Logs',
            'subtitle'         => 'Riwayat aktivitas sistem',
            'logs'             => $logs,
            'entityType'       => $entityType,
            'action'           => $action,
            'dateFrom'         => $dateFrom,
            'dateTo'           => $dateTo,
            'username'         => $username,
            'entityTypeList'   => $entityTypeList,
            'actionList'       => $actionList,
            'entityTypeLabels' => $entityTypeLabels,
            'actionLabels'     => $actionLabels,
        ];

        return view('logs/index', $data);
    }

    /**
     * Human-readable labels for entity types.
     */
    private function getEntityTypeLabels(): array
    {
        return [
            'auth'              => 'Authentication',
            'customer'          => 'Customer',
            'menu'              => 'Menu',
            'menu_options'      => 'Menu Options',
            'overhead'          => 'Overhead',
            'overhead_category' => 'Overhead Category',
            'payroll'           => 'Payroll',
            'purchase'          => 'Purchase',
            'recipe'            => 'Recipe',
            'sale'              => 'Sale',
            'user'              => 'User',
        ];
    }

    /**
     * Human-readable labels for actions.
     */
    private function getActionLabels(): array
    {
        return [
            'create'           => 'Created',
            'update'           => 'Updated',
            'delete'           => 'Deleted',
            'void'             => 'Voided',
            'bulk_save'        => 'Bulk Save',
            'toggle'           => 'Toggled',
            'login_success'    => 'Login Success',
            'login_fail'       => 'Login Failed',
            'logout'           => 'Logout',
            'forgot_email_sent'=> 'Password Reset Sent',
            'forgot_email_fail'=> 'Reset Email Failed',
            'forgot_throttled' => 'Reset Throttled',
            'forgot_unknown'   => 'Unknown Email Reset',
            'reset_success'    => 'Password Reset',
            'reset_fail'       => 'Reset Failed',
            'reset_invalid'    => 'Invalid Reset',
        ];
    }
}
