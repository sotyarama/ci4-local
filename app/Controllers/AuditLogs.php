<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;

class AuditLogs extends BaseController
{
    protected AuditLogModel $auditLogModel;

    public function __construct()
    {
        $this->auditLogModel = new AuditLogModel();
    }

    public function index()
    {
        $entityType = $this->request->getGet('entity_type') ?: null;
        $action     = $this->request->getGet('action') ?: null;
        $dateFrom   = $this->request->getGet('date_from') ?: null;
        $dateTo     = $this->request->getGet('date_to') ?: null;

        $builder = $this->auditLogModel
            ->select('audit_logs.*, users.username AS user_name')
            ->join('users', 'users.id = audit_logs.user_id', 'left')
            ->orderBy('audit_logs.created_at', 'DESC')
            ->orderBy('audit_logs.id', 'DESC');

        if ($entityType) {
            $builder->where('entity_type', $entityType);
        }
        if ($action) {
            $builder->where('action', $action);
        }
        if ($dateFrom) {
            $builder->where('DATE(audit_logs.created_at) >=', $dateFrom);
        }
        if ($dateTo) {
            $builder->where('DATE(audit_logs.created_at) <=', $dateTo);
        }

        $logs = $builder->findAll(200); // batasi 200 baris terbaru

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

        $data = [
            'title'          => 'Audit Log',
            'subtitle'       => 'Jejak perubahan data dan aktivitas sistem',
            'logs'           => $logs,
            'entityType'     => $entityType,
            'action'         => $action,
            'dateFrom'       => $dateFrom,
            'dateTo'         => $dateTo,
            'entityTypeList' => $entityTypeList,
            'actionList'     => $actionList,
        ];

        return view('audit_logs/audit_logs_index', $data);
    }
}
