<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPaymentFieldsToSales extends Migration
{
    public function up()
    {
        $this->forge->addColumn('sales', [
            'payment_method' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'cash',
                'after'      => 'customer_id',
            ],
            'amount_paid' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0,
                'after'      => 'payment_method',
            ],
            'change_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0,
                'after'      => 'amount_paid',
            ],
        ]);

        // Default untuk data lama.
        $this->db->query("UPDATE sales SET payment_method = 'cash' WHERE payment_method IS NULL OR payment_method = ''");
        $this->db->query('UPDATE sales SET amount_paid = total_amount WHERE amount_paid IS NULL OR amount_paid = 0');
        $this->db->query('UPDATE sales SET change_amount = (amount_paid - total_amount) WHERE change_amount IS NULL');
    }

    public function down()
    {
        $this->forge->dropColumn('sales', ['payment_method', 'amount_paid', 'change_amount']);
    }
}
