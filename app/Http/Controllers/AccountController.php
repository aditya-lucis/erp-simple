<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Account::orderBy('account_code', 'ASC');

            return DataTables::of($query)
                    ->addIndexColumn()
                    ->addColumn('balance', function ($row) {
                        return number_format(
                            $row->balance,
                            0,
                            ',',
                            '.'
                        );
                    })
                    ->addColumn('action', function ($item) {
                        return '<div class="d-flex gap-2 btn-icon-list">
                                        <a id="edit" class="btn btn-warning btn-circle" data-id="' . $item->id . '">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <a id="delete" class="btn btn-danger btn-circle" data-id="' . $item->id . '">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                        ';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }

        return view('coa.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'name' => 'required',
            'type' => 'required',
        ]);

        Account::create([
            'account_name'           => $validateData['name'],
            'normal_balance' => $validateData['type'],
            'account_code'   => generateCode('COA'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product berhasil disimpan!'
        ]);
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
        $coa = Account::find($id);

        if (!$coa) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($coa);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $coa = Account::find($id);

        if (!$coa) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        $validateData = $request->validate([
            'name' => 'required',
            'type' => 'required',
        ]);

        $coa->update([
            'account_name'   => $validateData['name'],
            'normal_balance' => $validateData['type']
        ]);

        return response()->json(['success' => true, 'message' => 'User berhasil diperbarui!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $coa = Account::find($id);

        if (!$coa) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        $coa->delete();

         return response()->json(['success' => true, 'message' => 'Data berhasil dihapus']);
    }
}
