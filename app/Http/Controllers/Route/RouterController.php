<?php

namespace App\Http\Controllers\Route;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Payment;
use Carbon\Carbon;

class RouterController extends Controller
{
    public function dashboard()
    {
        $months = collect(range(0, 11))->map(function ($i) {
            return Carbon::now()->subMonths($i)->format('F Y');
        })->reverse();

        // Aggregate completed payments by month and year
        $paymentData = Payment::selectRaw('CONCAT(paid_month, " ", paid_year) as period, SUM(amount) as total_amount')
            ->where('status', 'completed') // Only consider completed payments
            ->groupBy('period')
            ->pluck('total_amount', 'period');

        // Prepare chart data for each month in the past 12 months
        $chartData = $months->map(function ($month) use ($paymentData) {
            return $paymentData->get($month, 0);
        });

        // Calculate yearly earnings (completed only)
        $currentYear = (string)Carbon::now()->year;
        $lastYear = (string)Carbon::now()->subYear()->year;

        $currentYearEarning = Payment::where('paid_year', $currentYear)
            ->where('status', 'completed')
            ->sum('amount');
        $lastYearEarning = Payment::where('paid_year', $lastYear)
            ->where('status', 'completed')
            ->sum('amount');
        $previousYearEarning = Payment::whereNotIn('paid_year', [$currentYear, $lastYear])
            ->where('status', 'completed')
            ->sum('amount');

        $thisMonthEarning = Payment::where('paid_month', Carbon::now()->format('F'))
            ->where('paid_year', $currentYear)
            ->where('status', 'completed')
            ->sum('amount');

        $lastFivePayments = Payment::where('status', 'completed')
            ->orderBy('payment_id', 'desc')
            ->limit(5)
            ->get();
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

    public function cashier()
    {
        $cashiers = User::role('Receptionist')->get();
        return view('pages.home.cashier', compact([
            'cashiers'
        ]));
    }

    public function reports(Request $request)
    {
        // Retrieve filter inputs
        $tcbtNumber = $request->input('tcbt_student_number');   // e.g. "TCBT1001"
        $month = $request->input('month');                 // e.g. "January", "February"...
        $year = $request->input('year');                  // e.g. "2023", "2024"...

        // --- 1) Individual Student Payments ---
        // If a student TCBT number is provided, get that student's payments
        $studentPayments = collect();
        if ($tcbtNumber) {
            $student = Student::where('tcbt_student_number', $tcbtNumber)->first();
            if ($student) {
                // Order by created_at (or payment_date) as needed
                $studentPayments = $student->payments()->orderBy('created_at', 'desc')->get();
            }
        }

        // --- 2) All Students Paid/Unpaid for a Given Month/Year ---
        $filteredStudents = collect();
        if ($month && $year) {
            $filteredStudents = Student::all();

            foreach ($filteredStudents as $st) {
                // Check if there's at least one 'completed' payment record matching the requested month/year
                $hasPayment = $st->payments()
                    ->where('paid_month', $month)
                    ->where('paid_year', $year)
                    ->where('status', 'completed')
                    ->exists();

                // Add a temporary attribute to indicate the payment status
                $st->payment_status = $hasPayment ? 'Completed' : 'Unpaid';
            }
        }

        // --- 3) Total revenue (sum of amounts) for that month/year using created_at
        // We need to convert the month name (e.g. "January") into a numeric month (1).
        // If either $month or $year is missing, we'll default this to 0 or skip it.
        $totalPayments = 0;
        if ($month && $year) {
            try {
                $monthNumber = Carbon::parse($month)->month;  // "January" => 1, "February" => 2, etc.

                // Sum of the 'amount' from all payments created in that month/year
                $totalPayments = Payment::whereYear('created_at', $year)
                    ->whereMonth('created_at', $monthNumber)
                    ->sum('amount');
            } catch (\Exception $e) {
                // In case Carbon parse fails or month is invalid, total remains 0
                $totalPayments = 0;
            }
        }

        return view('pages.home.ReportsPage', compact(
            'tcbtNumber',
            'month',
            'year',
            'studentPayments',
            'filteredStudents',
            'totalPayments'
        ));
    }

    public function Books()
    {
        $books = Book::all();

        return view('pages.home.Books', compact([
            'books'
        ]));
    }
}
