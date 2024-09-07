<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Decision;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the transactions.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transactions = Transaction::with('decision.user')->get();
        return response()->json($transactions);
    }

    /**
     * Store a newly created transaction in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'decision_id' => 'required|exists:decisions,id',
            'currency' => 'required|string|max:3',
            'amount' => 'required|numeric',
            'payment_by' => 'required|string|max:50',
            'datetime' => 'required|date',
            'note' => 'nullable|string',
        ]);

        $transaction = Transaction::create($validatedData);

        return response()->json([
            'message' => 'Transaction created successfully',
            'transaction' => $transaction
        ], 201);
    }

    /**
     * Display the specified transaction.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        return response()->json($transaction->load('decision'));
    }

    /**
     * Update the specified transaction in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $transaction)
    {
        $validatedData = $request->validate([
            'decision_id' => 'sometimes|required|exists:decisions,id',
            'currency' => 'sometimes|required|string|max:3',
            'amount' => 'sometimes|required|numeric',
            'payment_by' => 'sometimes|required|string|max:50',
            'datetime' => 'sometimes|required|date',
            'note' => 'nullable|string',
        ]);

        $transaction->update($validatedData);

        return response()->json([
            'message' => 'Transaction updated successfully',
            'transaction' => $transaction
        ]);
    }

    /**
     * Remove the specified transaction from storage.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        $transaction->delete();

        return response()->json([
            'message' => 'Transaction deleted successfully'
        ]);
    }
}
