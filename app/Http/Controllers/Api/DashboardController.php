<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();

        $query = Transaction::where('user_id', $user->id);

        // Filter periode
        $period = $request->query('period', 'month');

        if ($period === 'range') {
            $start = $request->query('start_date');
            $end = $request->query('end_date');
            $query->whereBetween('date', [$start, $end]);
        } else {
            $now = now();

            match ($period) {
                'day' => $query->whereDate('date', $now->toDateString()),
                'week' => $query->whereBetween('date', [$now->startOfWeek(), $now->endOfWeek()]),
                'month' => $query->whereMonth('date', $now->month)->whereYear('date', $now->year),
                'year' => $query->whereYear('date', $now->year),
                default => null
            };
        }

        $transactions = $query->get();

        $total_income = $transactions->where('type', 'in')->sum('amount');
        $total_expense = $transactions->where('type', 'out')->sum('amount');
        $balance = $total_income - $total_expense;

        // Ringkasan harian
        $summary = $transactions->groupBy('date')->map(function ($group) {
            return [
                'date' => $group->first()->date,
                'income' => $group->where('type', 'in')->sum('amount'),
                'expense' => $group->where('type', 'out')->sum('amount'),
            ];
        })->values();

        // Grafik kategori
        $groupByCategory = $transactions->groupBy(fn($tx) => [$tx->type, optional($tx->category)->name]);

        $category_chart = [
            'income' => [],
            'expense' => [],
        ];

        foreach ($groupByCategory as $key => $items) {
            [$type, $category] = $key;
            $total = $items->sum('amount');
            $category_chart[$type][] = [
                'category' => $category,
                'total' => $total,
            ];
        }

        return response()->json([
            'balance' => $balance,
            'total_income' => $total_income,
            'total_expense' => $total_expense,
            'summary' => $summary,
            'category_chart' => $category_chart,
        ]);
    }
}
