<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Ledger;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class LedgerController extends Controller
{
    public function index(Request $request)
    {
        $accounts = Account::orderBy('account_name')->get();

        if ($request->ajax()) {
            $query = Ledger::with('account')
            ->when($request->account_id, function ($q) use ($request) {
                $q->where('account_id', $request->account_id);
            })
            ->when($request->from_date && $request->to_date, function ($q) use ($request) {
                $q->whereBetween('transaction_date', [
                    $request->from_date,
                    $request->to_date
                ]);
            })
            ->orderBy('transaction_date')
            ->orderBy('id');

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('transaction_date', function ($row) {
                    return $row->transaction_date->format('d-m-Y');
                })
                ->editColumn('amount', function ($row) {
                    return number_format($row->amount, 0, ',', '.');
                })
                ->editColumn('balance', function ($row) {
                    return number_format($row->balance, 0, ',', '.');
                })
                ->addColumn('debet', function ($row) {
                    return $row->position === 'debet'
                        ? number_format($row->amount, 0, ',', '.')
                        : '';
                })
                ->addColumn('kredit', function ($row) {
                    return $row->position === 'kredit'
                        ? number_format($row->amount, 0, ',', '.')
                        : '';
                })
                ->rawColumns(['debet','kredit'])
                ->make(true);
        }

        return view('ledgers.index', compact('accounts'));
    }

}
