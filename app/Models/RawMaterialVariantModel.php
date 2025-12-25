<?php

namespace App\Models;

use CodeIgniter\Model;

class RawMaterialVariantModel extends Model
{
    protected $table         = 'raw_material_variants';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'raw_material_id',
        'brand_id',
        'variant_name',
        'sku_code',
        'current_stock',
        'min_stock',
        'is_active',
    ];
}
