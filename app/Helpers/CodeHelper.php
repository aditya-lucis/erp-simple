<?php

use Illuminate\Support\Facades\DB;

if (! function_exists('generateCode')) {

    function generateCode(string $prefix): string
    {
        $date = now()->format('dmY');

        $sequence = DB::table('code_sequences')
            ->where('prefix', $prefix)
            ->where('date', $date)
            ->lockForUpdate()
            ->first();

        if (!$sequence) {
            DB::table('code_sequences')->insert([
                'prefix'      => $prefix,
                'date'        => $date,
                'last_number' => 1,
                'created_at'  => now(),
                'updated_at'  => now()
            ]);
            $number = 1;
        } else {
            $number = $sequence->last_number + 1;

            DB::table('code_sequences')
                ->where('id', $sequence->id)
                ->update([
                    'last_number' => $number,
                    'updated_at'  => now()
                ]);
        }

        return $prefix . $date . str_pad($number, 3, '0', STR_PAD_LEFT);
    }
}
