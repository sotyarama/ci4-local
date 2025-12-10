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
        $dateFrom   = $this->request->getGet('date_from') ?: null;
        $dateTo     = $this->request->getGet('date_to') ?: null;

        $builder = $this->auditLogModel
            ->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC');

        if ($entityType) {
            $builder->where('entity_type', $entityType);
        }
        if ($dateFrom) {
            $builder->where('DATE(created_at) >=', $dateFrom);
        }
        if ($dateTo) {
            $builder->where('DATE(created_at) <=', $dateTo);
        }

        $logs = $builder->findAll(200); // batasi 200 baris terbaru

        $data = [
            'title'       => 'Audit Log',
            'subtitle'    => 'Jejak perubahan menu & resep',
            'logs'        => $logs,
            'entityType'  => $entityType,
            'dateFrom'    => $dateFrom,
            'dateTo'      => $dateTo,
        ];

        return view('audit_logs/index', $data);
    }
}
