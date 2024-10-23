<?php

namespace App\Http\Controllers\Route;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\Payment;
use Carbon\Carbon;
class RouterController extends Controller
{
    public function dashboard()
    {
        $months = collect(range(0, 4))->map(function ($i) {
            return Carbon::now()->subMonths($i)->format('F');
        })->reverse();

        $paymentData = Payment::selectRaw('paid_month, SUM(amount) as total_amount')
            ->whereIn('paid_month', $months)
            ->groupBy('paid_month')
            ->pluck('total_amount', 'paid_month');

        $chartData = $months->map(function ($month) use ($paymentData) {
            return $paymentData->get($month, 0);
        });

        $currentYear = (string) Carbon::now()->year;
        $lastYear = (string) Carbon::now()->subYear()->year;

        $currentYearEarning = Payment::where('paid_year', $currentYear)->sum('amount');
        $lastYearEarning = Payment::where('paid_year', $lastYear)->sum('amount');

        $previousYearEarning = Payment::whereNotIn('paid_year', [$currentYear, $lastYear])->sum('amount');
        $thisMonthEarning = Payment::where('paid_month', Carbon::now()->format('F'))->sum('amount');

        $lastFivePayments = Payment::orderBy('payment_id', 'desc')->limit(5)->get();
        $totalStudents = Student::count();
        $totalActiveStudents = Student::where('status', 'Active')->count();
        $totalInactiveStudents = Student::where('status', 'Inactive')->count();
        $pendingPayments = Payment::where('status', 'Pending')->count();
        return view('pages.home.HomePage', [
            'months' => $months,
            'chartData' => $chartData,
            'yearEarning' => $currentYearEarning,
            'donutData' => [$currentYearEarning, $lastYearEarning, $previousYearEarning],
            'thisMonthEarning' => $thisMonthEarning,
            'lastFivePayments' => $lastFivePayments,
            'totalStudents' => $totalStudents,
            'totalActiveStudents' => $totalActiveStudents,
            'totalInactiveStudents' => $totalInactiveStudents,
            'pendingPayments' => $pendingPayments,

        ]);
    }

    public function students()
    {

        $allStudents = Student::orderBy('created_at', 'desc')->get();


        return view('pages.home.students', compact([
            'allStudents',
        ]));
    }

    public function addStudentPayment()
    {
        return view('pages.home.AddPayment');
    }

}
