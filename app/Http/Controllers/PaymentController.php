<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('student')->get();
        return response()->json($payments);
    }

    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'students_id' => 'required',
            'amount' => 'required|numeric',
            'paid_month' => 'required|string',
            'paid_year' => 'required|string',
            'status' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $student = Student::where('tcbt_student_number', $request->students_id)->first();

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found'], 404);
        }

        // Check if the student has already paid for the month
        $payment = Payment::where('students_id', $student->id)
            ->where('paid_month', $request->paid_month)
            ->where('paid_year', $request->paid_year)
            ->first();

        if ($payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment for this month has already been made',
            ], 422);
        }

        try {
            \DB::beginTransaction();

            $payment = Payment::create([
                'students_id' => $student->id,
                'amount' => $request->amount,
                'payment_date' => now(),
                'paid_month' => $request->paid_month,
                'paid_year' => $request->paid_year,
                'status' => 'completed',
                'processed_by' => Auth::user()->id,
            ]);

            $invoiceNumber = 'INV' . $student->tcbt_student_number . date('YmdHis');

            Invoice::create([
                'students_id' => $student->id,
                'invoice_number' => $invoiceNumber,
                'amount' => $request->amount,
                'payment_date' => now(),
                'processed_by' => Auth::user()->id,
                'paid_month' => $request->paid_month,
                'paid_year' => $request->paid_year,
                'status' => 'paid',
                'issue_date' => now(),
            ]);

            \DB::commit();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \DB::rollback();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);
        }
    }


    public function show($id)
    {
        $payment = Payment::with('student')->findOrFail($id);
        return response()->json($payment);
    }

    public function getPaymentByID($id)
    {
        $payment = Payment::where('payment_id', $id)->get();
        return response()->json($payment);
    }

    public function update(Request $request)
    {
        $payment = Payment::where('payment_id', $request->payment_id)->first();
        $payment->update($request->all());

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Payment deleted successfully',
        ]);
    }
}
