<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCustomerIdToSales extends Migration
{
    public function up()
    {
        $this->forge->addColumn('sales', [
            'customer_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'default'    => 1,
                'after'      => 'invoice_no',
            ],
        ]);

        $this->db->query('CREATE INDEX idx_sales_customer ON sales (customer_id)');
        $this->db->query('ALTER TABLE sales ADD CONSTRAINT fk_sales_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON UPDATE CASCADE ON DELETE RESTRICT');

        $this->db->query("UPDATE sales SET customer_id = 1 WHERE customer_id IS NULL");
        $this->db->query("UPDATE sales SET customer_name = 'Tamu' WHERE customer_name IS NULL OR customer_name = ''");
    }

    public function down()
    {
        $this->db->query('ALTER TABLE sales DROP FOREIGN KEY fk_sales_customer');
        $this->db->query('DROP INDEX idx_sales_customer ON sales');
        $this->forge->dropColumn('sales', 'customer_id');
    }
}
