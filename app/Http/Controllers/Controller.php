<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\User;
use Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;


    public function addCashier(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'nic' => 'required|unique:users,nic',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nic' => $request->nic,
        ]);

        // Assign 'Cashier' role to the user
        $user->assignRole('Receptionist');

        return response()->json([
            'message' => 'Cashier added successfully',
            'data' => $user,
            'status' => 200,
        ]);
    }

    public function getCashiers($id)
    {
        $cashier = User::findOrFail($id);

        return response()->json([
            'message' => 'Cashier fetched successfully',
            'cashier' => $cashier,
            'status' => 200,
        ]);
    }

    public function updateCashier(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'nic' => 'required|unique:users,nic,' . $id,
            'password' => 'nullable|min:6',
        ]);

        $cashier = User::findOrFail($id);
        $cashier->name = $request->name;
        $cashier->email = $request->email;
        $cashier->nic = $request->nic;

        if ($request->filled('password')) {
            $cashier->password = Hash::make($request->password);
        }

        $cashier->save();

        return response()->json([
            'message' => 'Cashier updated successfully',
            'status' => 200,
        ]);
    }

    public function generateReport(Request $request)
    {
        $reportType = $request->input('reportType');
        $studentNumber = $request->input('tcbtStudentNumber');
        $date = $request->input('date');
        $month = $request->input('month');

        $query = Payment::query()->with('student');

        if ($reportType === 'student_payments' && $studentNumber) {
            $query->whereHas('student', function ($q) use ($studentNumber) {
                $q->where('tcbt_student_number', $studentNumber);
            });
        } elseif ($reportType === 'today_payments' && $date) {
            $query->whereDate('payment_date', $date);
        } elseif ($reportType === 'monthly_payments' && $month) {
            $query->whereMonth('payment_date', date('m', strtotime($month)))
                ->whereYear('payment_date', date('Y', strtotime($month)));
        }

        $payments = $query->get();

        if ($payments->isEmpty()) {
            return response()->json(['success' => false]);
        }

        // Optionally generate CSV for download
        $downloadUrl = $this->generateCSV($payments);

        return response()->json(['success' => true, 'data' => $payments, 'download_url' => $downloadUrl]);
    }

    protected function generateCSV($payments)
    {
        $filename = 'payments_report_' . now()->timestamp . '.csv';
        $path = storage_path('app/public/' . $filename);

        $file = fopen($path, 'w');
        fputcsv($file, ['Payment ID', 'Student Name', 'Amount', 'Status', 'Paid Month', 'Payment Date']);

        foreach ($payments as $payment) {
            fputcsv($file, [
                $payment->payment_id,
                $payment->student->name,
                $payment->amount,
                $payment->status,
                $payment->paid_month . '/' . $payment->paid_year,
                $payment->payment_date,
            ]);
        }

        fclose($file);

        return asset('storage/' . $filename);
    }

}
