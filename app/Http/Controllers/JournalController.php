<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\JournalDetail;
use App\Models\JournalHeader;
use App\Models\Ledger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class JournalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = JournalHeader::with('details')
            ->select('journal_headers.*');

            if ($request->from_date && $request->to_date) {
                $query->whereBetween('journal_date', [
                    $request->from_date,
                    $request->to_date
                ]);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('total_debet', function ($row) {
                    return number_format(
                        $row->details->where('position', 'debet')->sum('amount'),
                        0,
                        ',',
                        '.'
                    );
                })
                ->addColumn('total_kredit', function ($row) {
                    return number_format(
                        $row->details->where('position', 'kredit')->sum('amount'),
                        0,
                        ',',
                        '.'
                    );
                })
                ->editColumn('journal_date', function ($row) {
                    return $row->journal_date->format('d-m-Y');
                })
                ->make(true);
        }

            return view('gej.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
         $accounts = Account::orderBy('account_name')->get();
         return view('gej.create', compact('accounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'journal_date' => 'required|date',
                'description'  => 'required',
                'account_id.*' => 'required',
                'position.*'   => 'required',
                'amount.*'     => 'required|numeric|min:0'
            ]);

            // Validasi debet = kredit
            $totalDebet  = 0;
            $totalKredit = 0;

            foreach ($request->position as $i => $pos) {
                if ($pos == 'debet') {
                    $totalDebet += $request->amount[$i];
                } else {
                    $totalKredit += $request->amount[$i];
                }
            }

            if ($totalDebet != $totalKredit) {
                return back()->with('error', 'Total debet dan kredit harus sama!');
            }

             // Simpan jurnal
            $journal = JournalHeader::create([
                'journal_code' => generateCode('GEJ'),
                'journal_date' => $request->journal_date,
                'description'  => $request->description
            ]);

            foreach ($request->account_id as $i => $accountId) {
                $detail = JournalDetail::create([
                    'journal_header_id' => $journal->id,
                    'account_id' => $accountId,
                    'position'   => $request->position[$i],
                    'amount'     => $request->amount[$i],
                ]);

                $account = Account::find($accountId);
                $saldo   = $account->balance;

                if ($detail->position == $account->normal_balance) {
                    $saldo += $detail->amount;
                } else {
                    $saldo -= $detail->amount;
                }

                Ledger::create([
                    'account_id'       => $account->id,
                    'transaction_date' => $journal->journal_date,
                    'reference'        => $journal->journal_code,
                    'position'         => $detail->position,
                    'amount'           => $detail->amount,
                    'balance'          => $saldo
                ]);

                $account->update(['balance' => $saldo]);
            }

            DB::commit();
            return redirect()->route('generaljournal.index')->with('success', 'Jurnal berhasil disimpan dan diposting');
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
