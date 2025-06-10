<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;

class TransactionController extends Controller
{
    public function index()
    {
        return auth()->user()->transactions()->with('category')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:in,out',
            'amount' => 'required|numeric',
            'date' => 'required|date',
        ]);

        $data['user_id'] = auth()->id();

        return Transaction::create($data);
    }

    public function show(Transaction $transaction)
    {
        return $transaction->load('category');
    }

    public function update(Request $request, Transaction $transaction)
    {
        $transaction->update($request->only('category_id', 'type', 'amount', 'date'));
        return $transaction;
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        return response()->noContent();
    }
}
