<?php

namespace App\Imports;

use App\Models\Bonus;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BonusImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if (empty($row['user_id']) || empty($row['amount'])) {
            throw new \Exception("A row contains empty fields. Please check your file.");
        }

        return new Bonus([
            'user_id'  => $row['user_id'],
            'bonus_by' => 1,
            'type'     => 'Site Special Bonus',
            'amount'   => $row['amount'],
        ]);
    }
}
