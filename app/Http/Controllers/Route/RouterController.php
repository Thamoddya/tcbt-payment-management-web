<?php

namespace App\Http\Controllers\Route;

use App\Http\Controllers\Controller;
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
        $currentYear = (string) Carbon::now()->year;
        $lastYear = (string) Carbon::now()->subYear()->year;

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

    public function Reports(Request $request)
    {
        // Get the month and year from the request (default to current month and year)
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        // Get all students
        $students = Student::with('payments') // Eager load payments
            ->get();

        // Filter the students based on the selected month and year
        $filteredStudents = $students->map(function ($student) use ($month, $year) {
            // Get the registration date and check the payment status for the selected month and year
            $registerDate = Carbon::parse($student->registration_date);

            // Generate 12 months from the registration date
            $months = collect();
            for ($i = 0; $i < 12; $i++) {
                $months->push($registerDate->copy()->addMonths($i));
            }

            // Check if the selected month exists in the student's payment months
            $hasPayment = $student->payments->some(function ($payment) use ($month, $year) {
                return Carbon::parse($payment->payment_date)->month == $month
                    && Carbon::parse($payment->payment_date)->year == $year;
            });

            // Add a 'payment_status' attribute to the student based on whether they've paid
            $student->payment_status = $hasPayment ? 'Paid' : 'Due';

            return $student;
        });
        return view('pages.home.ReportsPage', compact([
            'filteredStudents'
        ]));
    }
}
