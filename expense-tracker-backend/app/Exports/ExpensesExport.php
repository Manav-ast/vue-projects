<?php

namespace App\Exports;

use App\Models\Expenses;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\Auth;

class ExpensesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $user = Auth::user();
        return Expenses::where('user_id', $user->id)
            ->with('group')
            ->get()
            ->map(function ($expense) use ($user) {
                return [
                    'User Name' => $user->name,
                    'Name' => $expense->name,
                    'Amount' => $expense->amount,
                    'Date' => $expense->date,
                    'Group' => $expense->group ? $expense->group->name : 'No Group'
                ];
            });
    }

    public function headings(): array
    {
        return [
            'User Name',
            'Name',
            'Amount',
            'Date',
            'Group'
        ];
    }
}
