<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Account::insert([
        [
            'account_code' => generateCode('COA'),
            'account_name' => 'Persediaan Farmasi',
            'normal_balance' => 'debet'
        ],
        [
            'account_code' => generateCode('COA'),
            'account_name' => 'PPN Masukan',
            'normal_balance' => 'debet'
        ],
        [
            'account_code' => generateCode('COA'),
            'account_name' => 'Hutang Usaha',
            'normal_balance' => 'kredit'
        ],
    ]);

    }
}
