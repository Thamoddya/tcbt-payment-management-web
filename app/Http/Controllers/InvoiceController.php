<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('student')->get();
        return response()->json($invoices);
    }

    public function store(Request $request)
    {
        $request->validate([
            'students_id' => 'required|exists:students,id',
            'invoice_number' => 'required|unique:invoices,invoice_number',
            'amount' => 'required',
            'status' => 'required|in:unpaid,paid,overdue',
            'issue_date' => 'required',
        ]);

        $invoice = Invoice::create($request->all());

        return response()->json(['message' => 'Invoice created successfully', 'data' => $invoice], 201);
    }

    public function show($id)
    {
        $invoice = Invoice::with('student')->findOrFail($id);
        return response()->json($invoice);
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->update($request->all());

        return response()->json(['message' => 'Invoice updated successfully', 'data' => $invoice]);
    }

    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();

        return response()->json(['message' => 'Invoice deleted successfully']);
    }
}
