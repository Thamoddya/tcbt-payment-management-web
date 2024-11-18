<?php

namespace App\Http\Controllers;

use App\Models\Student;
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
            $payments = $student->payments()->orderBy('created_at', 'desc')->get();

            return response()->json([
                'student' => $student,
                'payments' => $payments,
            ]);
        } else {
            return response()->json(['error' => 'Student not found'], 404);
        }
    }
}
