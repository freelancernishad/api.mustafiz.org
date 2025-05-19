<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Decision;
use App\Models\Transaction;

class ReportController extends Controller
{
    public function getDashboardStats()
    {
        // Total user registrations
        $totalRegistrations = User::count();

        // Count per user category
        $categories = ['student', 'senior', 'disable', 'orphan', 'homeless', 'refugee', 'other'];
        $categoryCounts = [];
        foreach ($categories as $category) {
            $categoryCounts[$category] = User::where('category', $category)->count();
        }

        // Decisions by status
        $decisionStatuses = ['pending', 'approved', 'rejected'];
        $decisionCounts = [];
        foreach ($decisionStatuses as $status) {
            $decisionCounts[$status] = Decision::where('status', $status)->count();
        }

        // Total how_much from approved decisions
        $totalRequestedAmount = Decision::where('status', 'approved')
            ->sum(DB::raw('CAST(how_much AS DECIMAL(12,2))'));

        // Total approved_amount from approved decisions
        $totalApprovedAmount = Decision::where('status', 'approved')
            ->sum(DB::raw('CAST(approved_amount AS DECIMAL(12,2))'));

        // Total amount from all transactions
        $totalGivenAmount = Transaction::sum(DB::raw('CAST(amount AS DECIMAL(12,2))'));

        return response()->json([
            'total_applicants' => $totalRegistrations,
            'categories' => $categoryCounts,
            'total_applications' => $decisionCounts,
            'total_requested_amount' => $totalRequestedAmount,
            'total_approved_amount' => $totalApprovedAmount,
            'total_given_amount' => $totalGivenAmount,
        ]);
    }
}
