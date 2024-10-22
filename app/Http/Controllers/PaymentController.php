<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('student')->get();
        return response()->json($payments);
    }

    public function store(Request $request)
    {
        $request->validate([
            'students_id' => 'required|exists:students,id',
            'amount' => 'required|integer',
            'payment_date' => 'required',
            'status' => 'required|in:completed,pending,failed',
        ]);

        $payment = Payment::create($request->all());

        return response()->json(['message' => 'Payment created successfully', 'data' => $payment], 201);
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
