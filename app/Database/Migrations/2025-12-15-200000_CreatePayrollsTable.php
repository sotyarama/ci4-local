<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePayrollsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'period_month' => [
                'type'       => 'VARCHAR',
                'constraint' => 7, // YYYY-MM
            ],
            'pay_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        // unique per staff per periode
        $this->forge->addUniqueKey(['user_id', 'period_month'], 'uniq_user_period');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('payrolls', true);
    }

    public function down()
    {
        $this->forge->dropTable('payrolls', true);
    }
}
