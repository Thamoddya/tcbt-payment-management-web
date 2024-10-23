<?php

namespace App\Http\Controllers;

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
            'amount' => 'required',
            'paid_month' => 'required',
            'paid_year' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        $student = Student::where('tcbt_student_number', $request->students_id)->first();

        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        try {
            Payment::create([
                'students_id' => $student->id,
                'amount' => $request->amount,
                'payment_date' => date('Y-m-d'),
                'paid_month' => $request->paid_month,
                'paid_year' => $request->paid_year,
                'status' => 'completed',
                'processed_by' => 1,
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'line' => $e->getLine()]);
        }
    }

    public function show($id)
    {
        $payment = Payment::with('student')->findOrFail($id);
        return response()->json($payment);
    }

    public function update(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);
        $payment->update($request->all());

        return response()->json(['message' => 'Payment updated successfully', 'data' => $payment]);
    }

    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();

        return response()->json(['message' => 'Payment deleted successfully']);
    }
}
