<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    /**
     * Display the attendance management page.
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $selectedDate = $request->input('date', Carbon::today()->format('Y-m-d'));
        $status = $request->input('status', 'all');
        $selectedGrade = $request->input('grade');

        // Convert to Carbon instance for easier manipulation
        $date = Carbon::parse($selectedDate);

        // Get all active students
        $studentsQuery = Student::where('status', 1);

        // Apply grade filter if selected
        if ($selectedGrade) {
            $studentsQuery->where('grade', $selectedGrade);
        }

        $students = $studentsQuery->get();

        // Get attendance records for the selected date
        $attendanceRecords = Attendance::where('date', $date->format('Y-m-d'))->get()
            ->keyBy('student_id');

        // Prepare attendance data with student information
        $attendanceData = [];
        foreach ($students as $student) {
            // Check if student has attendance record for the selected date
            $isPresent = isset($attendanceRecords[$student->id]);
            $attendanceRecord = $isPresent ? $attendanceRecords[$student->id] : null;

            // Get last payment info to determine payment status
            $lastPayment = $student->getStudentLastPayment();
            $paymentStatus = $this->determinePaymentStatus($lastPayment);

            // Create record with all required information
            $record = [
                'student' => $student,
                'is_present' => $isPresent,
                'attendance_time' => $attendanceRecord ? Carbon::parse($attendanceRecord->created_at)->format('h:i A') : null,
                'attendance_id' => $attendanceRecord ? $attendanceRecord->id : null,
                'payment_status' => $paymentStatus
            ];

            // Apply status filter
            if (
                $status === 'all' ||
                ($status === 'present' && $isPresent) ||
                ($status === 'absent' && !$isPresent)
            ) {
                $attendanceData[] = $record;
            }
        }

        // Get unique grades for the filter dropdown
        $grades = Student::distinct('grade')->pluck('grade')->sort()->values();

        // Calculate attendance statistics
        $totalStudents = count($students);
        $presentCount = $attendanceRecords->count();
        $absentCount = $totalStudents - $presentCount;

        $presentPercentage = $totalStudents > 0 ? round(($presentCount / $totalStudents) * 100, 1) : 0;
        $absentPercentage = $totalStudents > 0 ? round(($absentCount / $totalStudents) * 100, 1) : 0;

        // Calculate weekly average attendance
        $weeklyAverage = $this->calculateWeeklyAverage($date);

        // Prepare chart data
        $weeklyChartData = $this->getWeeklyChartData($date);
        $gradeChartData = $this->getGradeChartData($selectedDate, $students, $attendanceRecords);
        $paymentChartData = $this->getPaymentChartData($students);

        return view('pages.home.attendance', compact(
            'attendanceData',
            'selectedDate',
            'status',
            'grades',
            'totalStudents',
            'presentCount',
            'absentCount',
            'presentPercentage',
            'absentPercentage',
            'weeklyAverage',
            'weeklyChartData',
            'gradeChartData',
            'paymentChartData'
        ));
    }

    /**
     * Mark attendance for a student.
     */
    public function markAttendance(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent'
        ]);

        $studentId = $request->input('student_id');
        $date = Carbon::parse($request->input('date'))->format('Y-m-d');
        $status = $request->input('status');

        // Check if attendance record already exists
        $existingRecord = Attendance::where('student_id', $studentId)
            ->where('date', $date)
            ->first();

        if ($status === 'present') {
            // Create or update attendance record
            if ($existingRecord) {
                // Record exists, return success without changes
                return response()->json([
                    'success' => true,
                    'message' => 'Attendance already marked as present.'
                ]);
            } else {
                // Create new attendance record
                Attendance::create([
                    'student_id' => $studentId,
                    'date' => $date,
                    'marked_by' => Auth::id()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Student marked as present successfully.'
                ]);
            }
        } else {
            // Remove attendance record if exists (mark as absent)
            if ($existingRecord) {
                $existingRecord->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Student marked as absent successfully.'
                ]);
            } else {
                // Already absent
                return response()->json([
                    'success' => true,
                    'message' => 'Student is already marked as absent.'
                ]);
            }
        }
    }

    /**
     * Perform bulk attendance actions.
     */
    public function bulkAttendance(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'action' => 'required|in:present,absent,clear',
            'grade' => 'nullable|string'
        ]);

        $date = Carbon::parse($request->input('date'))->format('Y-m-d');
        $action = $request->input('action');
        $grade = $request->input('grade');

        // Get all active students, filtered by grade if specified
        $studentsQuery = Student::where('status', 1);

        if ($grade) {
            $studentsQuery->where('grade', $grade);
        }

        $students = $studentsQuery->get();

        if ($action === 'clear') {
            // Delete all attendance records for the selected date
            Attendance::where('date', $date)
                ->whereIn('student_id', $students->pluck('id'))
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Attendance records cleared successfully.'
            ]);
        } elseif ($action === 'present') {
            // Mark all students as present
            foreach ($students as $student) {
                Attendance::firstOrCreate([
                    'student_id' => $student->id,
                    'date' => $date
                ], [
                    'marked_by' => Auth::id()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'All students marked as present successfully.'
            ]);
        } elseif ($action === 'absent') {
            // Mark all students as absent by deleting attendance records
            Attendance::where('date', $date)
                ->whereIn('student_id', $students->pluck('id'))
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'All students marked as absent successfully.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid action specified.'
        ]);
    }

    /**
     * Get attendance history for a student.
     */
    public function getAttendanceHistory(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id'
        ]);

        $studentId = $request->input('student_id');

        // Get last 30 days of attendance records
        $from = Carbon::now()->subDays(30)->format('Y-m-d');
        $to = Carbon::now()->format('Y-m-d');

        $attendanceRecords = Attendance::where('student_id', $studentId)
            ->whereBetween('date', [$from, $to])
            ->orderBy('date', 'desc')
            ->get();

        // Format records with additional info
        $history = [];
        $dateRange = [];

        // Create array of all dates in the range
        $current = Carbon::parse($from);
        $end = Carbon::parse($to);

        while ($current <= $end) {
            $dateStr = $current->format('Y-m-d');
            $dateRange[$dateStr] = $dateStr;
            $current->addDay();
        }

        // Map attendance records to dates
        $recordsByDate = $attendanceRecords->keyBy('date');

        // Get all users who marked attendance
        $markerIds = $attendanceRecords->pluck('marked_by')->filter()->unique();
        $markers = User::whereIn('id', $markerIds)->get()->keyBy('id');

        // Create history with all dates (present or absent)
        foreach ($dateRange as $date) {
            $record = $recordsByDate->get($date);
            $history[] = [
                'date' => $date,
                'status' => $record ? 'present' : 'absent',
                'marked_by' => $record ? $record->marked_by : null,
                'marked_by_name' => $record && $record->marked_by ? $markers[$record->marked_by]->name : null,
                'created_at' => $record ? $record->created_at : null,
            ];
        }

        return response()->json([
            'success' => true,
            'history' => $history
        ]);
    }

    /**
     * Determine payment status based on last payment.
     */
    private function determinePaymentStatus($lastPayment)
    {
        if (!$lastPayment) {
            return 'none';
        }

        $currentMonth = Carbon::now()->format('F');
        $currentYear = Carbon::now()->year;

        if ($lastPayment->paid_month === $currentMonth && $lastPayment->paid_year == $currentYear) {
            return 'current';
        }

        return 'overdue';
    }

    /**
     * Calculate weekly average attendance rate.
     */
    private function calculateWeeklyAverage($date)
    {
        // Get the past 7 days excluding today
        $dates = [];
        for ($i = 6; $i >= 0; $i--) {
            $dates[] = Carbon::parse($date)->subDays($i)->format('Y-m-d');
        }

        $totalStudents = Student::where('status', 1)->count();

        if ($totalStudents === 0) {
            return 0;
        }

        $attendanceByDate = Attendance::whereIn('date', $dates)
            ->select('date', DB::raw('count(*) as count'))
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $total = 0;
        foreach ($dates as $day) {
            $count = isset($attendanceByDate[$day]) ? $attendanceByDate[$day]->count : 0;
            $rate = $totalStudents > 0 ? ($count / $totalStudents) * 100 : 0;
            $total += $rate;
        }

        return round($total / count($dates), 1);
    }

    /**
     * Get weekly attendance chart data.
     */
    private function getWeeklyChartData($date)
    {
        // Get the past 7 days including today
        $labels = [];
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $currentDate = Carbon::parse($date)->subDays($i);
            $dateStr = $currentDate->format('Y-m-d');
            $labels[] = $currentDate->format('M d');

            $totalStudents = Student::where('status', 1)->count();
            $presentCount = Attendance::where('date', $dateStr)->count();

            $rate = $totalStudents > 0 ? round(($presentCount / $totalStudents) * 100, 1) : 0;
            $data[] = $rate;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Get grade-wise attendance chart data.
     */
    private function getGradeChartData($date, $students, $attendanceRecords)
    {
        // Group students by grade
        $gradeGroups = $students->groupBy('grade');

        $labels = [];
        $presentData = [];
        $absentData = [];

        foreach ($gradeGroups as $grade => $gradeStudents) {
            $labels[] = 'Grade ' . $grade;

            $presentCount = 0;
            foreach ($gradeStudents as $student) {
                if (isset($attendanceRecords[$student->id])) {
                    $presentCount++;
                }
            }

            $absentCount = count($gradeStudents) - $presentCount;

            $presentData[] = $presentCount;
            $absentData[] = $absentCount;
        }

        return [
            'labels' => $labels,
            'present' => $presentData,
            'absent' => $absentData
        ];
    }

    /**
     * Get payment status chart data.
     */
    private function getPaymentChartData($students)
    {
        $paidCount = 0;
        $overdueCount = 0;
        $noPaymentCount = 0;

        foreach ($students as $student) {
            $lastPayment = $student->getStudentLastPayment();
            $status = $this->determinePaymentStatus($lastPayment);

            if ($status === 'current') {
                $paidCount++;
            } elseif ($status === 'overdue') {
                $overdueCount++;
            } else {
                $noPaymentCount++;
            }
        }

        return [
            'data' => [$paidCount, $overdueCount, $noPaymentCount]
        ];
    }
}
