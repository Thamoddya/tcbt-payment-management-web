<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    public function CatchierLogin(Request $request)
    {
        $validate = $request->validate([
            'nic' => 'required',
            'password' => 'required'
        ]);

        $credentials = $request->only('nic', 'password');

        if (Auth::attempt($credentials)) {
            $token = Auth::user()->createToken('authToken')->plainTextToken;

            return response()->json([
                'token' => $token,
                'message' => 'Login successful!',
                'code' => 200
            ], 200);
        }

        return response()->json(['message' => 'The provided credentials do not match our records.'], 401);
    }

    public function User(Request $request)
    {
        $user = \auth()->user();

        return response()->json([
            'user' => $user,
            'code' => 200
        ], 200);
    }

    public function AddAttendance(Request $request)
    {
        $validate = $request->validate([
            'tcbt_student_number' => 'required',
        ]);

        $todayDate = date('Y-m-d');
        $studentData = Student::where('tcbt_student_number', $request->tcbt_student_number)
            ->first();

        if (!$studentData) {
            return response()->json([
                'message' => 'Student not found!',
                'code' => 404
            ], 404);
        }

        $attendanceExists = Attendance::where('student_id', $studentData->id)
            ->where('date', $todayDate)
            ->exists();

        if ($attendanceExists) {
            return response()->json([
                'message' => 'Attendance already marked for today!',
                'student' => $studentData,
                'code' => 200,
                'last_paid' => $studentData->getStudentLastPayment()
            ], 200);
        }

        Attendance::create([
            'student_id' => $studentData->id,
            'date' => $todayDate,
            'marked_by' => auth()->user()->id
        ]);

        return response()->json([
            'student' => $studentData,
            'message' => 'Attendance added successfully!',
            'code' => 200,
            'last_paid' => $studentData->getStudentLastPayment()
        ], 200);
    }
}
