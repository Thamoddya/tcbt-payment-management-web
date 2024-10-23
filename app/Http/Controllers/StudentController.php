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

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        $student->update($request->all());

        return response()->json(['message' => 'Student updated successfully', 'data' => $student]);
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();

        return response()->json(['message' => 'Student deleted successfully']);
    }
}
