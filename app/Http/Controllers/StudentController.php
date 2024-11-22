<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::all();
        return response()->json($students);
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_no' => 'required|string|max:10|min:10|unique:students,contact_no',
            'grade' => 'required|string|max:50',
            'school' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'parent_contact_no' => 'sometimes|max:15',
            'parent_name' => 'sometimes|max:255',
            'need_to_pay' => 'required|numeric',
            'registration_date' => 'required|date',
        ]);

        //Generate TCBT Student Number TCBT-YEAR-RANDOM 10 digits
        $year = date('Y');
        $random = mt_rand(1000000000, 9999999999);
        $tcbt_student_number = "TCBT$year-$random";


        // Create a new student record
        Student::create([
            'tcbt_student_number' => $tcbt_student_number,
            'name' => $request->name,
            'contact_no' => $request->contact_no,
            'grade' => $request->grade,
            'school' => $request->school,
            'address' => $request->address,
            'parent_contact_no' => $request->parent_contact_no,
            'parent_name' => $request->parent_name,
            'need_to_pay' => $request->need_to_pay,
            'registration_date' => $request->registration_date,
        ]);

        // Return a success response
        return response()->json(['success' => 'Student added successfully.']);
    }

    public function show($id)
    {
        $student = Student::findOrFail($id);
        $studentPayments = $student->payments;

        return response()->json([
            'student' => $student,
            'payments' => $studentPayments,
        ]);
    }

    public function update(Request $request)
    {
        $student = Student::where('tcbt_student_number', $request->tcbt_student_number)->first();

        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_no' => 'required|string|max:10|min:10|unique:students,contact_no,' . $student->id,
            'grade' => 'required|string|max:50',
            'school' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'parent_contact_no' => 'nullable|string|max:15',
            'parent_name' => 'nullable|string|max:255',
            'status' => 'required|boolean',
            'need_to_pay' => 'required|numeric',
            'registration_date' => 'required|date',
        ]);

        // Update student data
        $student->update([
            'name' => $request->name,
            'contact_no' => $request->contact_no,
            'grade' => $request->grade,
            'school' => $request->school,
            'address' => $request->address,
            'parent_contact_no' => $request->parent_contact_no,
            'parent_name' => $request->parent_name,
            'status' => $request->status,
            'need_to_pay' => $request->need_to_pay,
            'registration_date' => $request->registration_date,
        ]);

        // Return success response
        return response()->json(['success' => 'Student updated successfully.']);
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();

        return response()->json(['message' => 'Student deleted successfully']);
    }

    public function getStudentDetails($tcbt_student_number)
    {
        $student = Student::where('tcbt_student_number', $tcbt_student_number)->first();

        if ($student) {
            // Fetch payments related to the student
            $payments = $student->payments()->orderBy('created_at', 'desc')->get();

            // Get the student's registration date
            $registrationDate = Carbon::parse($student->registration_date);

            // Set the start and end months based on the registration date
            $startMonth = $registrationDate; // Start from the registration month
            $currentMonth = Carbon::now();  // Current month for comparison

            // Create month range from registration month to current month + 12 future months
            $endMonth = $currentMonth->copy()->addMonths(12);  // Show payments for 12 months after the current month

            $monthData = [];

            // Loop through the months from the registration month to the end (current month + 12)
            while ($startMonth->lte($endMonth)) {
                $monthKey = $startMonth->format('Y-m');

                // Check if there's a payment for this month
                $payment = $payments->first(function ($pay) use ($startMonth) {
                    return strtolower(trim($pay->paid_month)) === strtolower($startMonth->format('F')) &&
                        intval($pay->paid_year) === intval($startMonth->year);
                });

                // Determine payment status
                if ($payment) {
                    // If payment exists, check its status
                    $monthData[] = [
                        'month' => $startMonth->format('F Y'),
                        'amount' => $payment->amount,
                        'status' => $payment->status, // Paid or Due
                        'payment_date' => $payment->status === 'Paid' ? $payment->payment_date : null, // Only show payment date if paid
                    ];
                } else {
                    // If no payment, check if the month is past, current, or future
                    if ($startMonth->isFuture()) {
                        // For future months, mark as "Pending"
                        $monthData[] = [
                            'month' => $startMonth->format('F Y'),
                            'amount' => $student->need_to_pay,
                            'status' => 'Pending', // Future payments are pending
                            'payment_date' => null
                        ];
                    } else {
                        // For past months (up to the current month), mark as "Due"
                        $monthData[] = [
                            'month' => $startMonth->format('F Y'),
                            'amount' => $student->need_to_pay,
                            'status' => 'Due', // Past payments are due
                            'payment_date' => null
                        ];
                    }
                }

                // Move to the next month
                $startMonth->addMonth();
            }

            return response()->json([
                'student' => $student,
                'payments' => $monthData,
            ]);
        } else {
            return response()->json(['error' => 'Student not found'], 404);
        }
    }



}
