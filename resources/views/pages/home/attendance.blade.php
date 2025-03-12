@extends('layouts.MainLayout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Attendance Management</h4>

                        <!-- Date Selection and Filter Form -->
                        <form id="attendanceFilterForm" class="mb-4">
                            <div class="row g-3 align-items-center">
                                <div class="col-md-3">
                                    <label for="attendanceDate" class="form-label">Select Date</label>
                                    <input type="date" class="form-control" id="attendanceDate" name="date"
                                           value="{{ $selectedDate ?? date('Y-m-d') }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="filterStatus" class="form-label">Filter Status</label>
                                    <select class="form-select" id="filterStatus" name="status">
                                        <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All Students
                                        </option>
                                        <option value="present" {{ $status == 'present' ? 'selected' : '' }}>Present
                                            Only
                                        </option>
                                        <option value="absent" {{ $status == 'absent' ? 'selected' : '' }}>Absent Only
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filterGrade" class="form-label">Filter Grade</label>
                                    <select class="form-select" id="filterGrade" name="grade">
                                        <option value="">All Grades</option>
                                        @foreach($grades as $grade)
                                            <option
                                                value="{{ $grade }}" {{ request('grade') == $grade ? 'selected' : '' }}>{{ $grade }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary me-2">Apply Filters</button>
                                    <button type="button" id="printAttendance" class="btn btn-outline-secondary">Print
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Attendance Stats Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h5 class="card-title text-white">Total Students</h5>
                                        <h2 class="mb-0">{{ $totalStudents }}</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h5 class="card-title text-white">Present</h5>
                                        <h2 class="mb-0">{{ $presentCount }}</h2>
                                        <small>{{ $presentPercentage }}% of total</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body">
                                        <h5 class="card-title text-white">Absent</h5>
                                        <h2 class="mb-0">{{ $absentCount }}</h2>
                                        <small>{{ $absentPercentage }}% of total</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <h5 class="card-title text-white">Last 7 Days Avg</h5>
                                        <h2 class="mb-0">{{ $weeklyAverage }}%</h2>
                                        <small>Average attendance rate</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bulk Actions Section -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h5 class="mb-0">Bulk Actions</h5>
                                            </div>
                                            <div>
                                                <button type="button" id="markAllPresent" class="btn btn-success me-2">
                                                    Mark All Present
                                                </button>
                                                <button type="button" id="markAllAbsent" class="btn btn-danger me-2">
                                                    Mark All Absent
                                                </button>
                                                <button type="button" id="clearAttendance" class="btn btn-warning">Clear
                                                    All
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Attendance Table -->
                        <div class="table-responsive">
                            <table id="attendanceTable" class="table table-striped table-hover">
                                <thead>
                                <tr>
                                    <th>Student ID</th>
                                    <th>Name</th>
                                    <th>Grade</th>
                                    <th>Status</th>
                                    <th>Payment Status</th>
                                    <th>Time</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($attendanceData as $record)
                                    <tr class="{{ $record['is_present'] ? 'table-success' : 'table-danger' }}">
                                        <td>{{ $record['student']->tcbt_student_number }}</td>
                                        <td>{{ $record['student']->name }}</td>
                                        <td>{{ $record['student']->grade }}</td>
                                        <td>
                                            @if($record['is_present'])
                                                <span class="badge bg-success">Present</span>
                                            @else
                                                <span class="badge bg-danger">Absent</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($record['payment_status'] === 'current')
                                                <span class="badge bg-success">Paid</span>
                                            @elseif($record['payment_status'] === 'overdue')
                                                <span class="badge bg-danger">Overdue</span>
                                            @else
                                                <span class="badge bg-warning">No Payment</span>
                                            @endif
                                        </td>
                                        <td>{{ $record['attendance_time'] ?? 'N/A' }}</td>
                                        <td>
                                            @if($record['is_present'])
                                                <button type="button" class="btn btn-danger btn-sm mark-attendance"
                                                        data-student-id="{{ $record['student']->id }}"
                                                        data-status="absent">
                                                    Mark Absent
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-success btn-sm mark-attendance"
                                                        data-student-id="{{ $record['student']->id }}"
                                                        data-status="present">
                                                    Mark Present
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-info btn-sm"
                                                    onclick="viewStudent({{ $record['student']->id }})">
                                                View
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No attendance records found for the selected
                                            date.
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Weekly Attendance Chart Card -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Weekly Attendance Trend</h5>
                        <canvas id="weeklyAttendanceChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grade-wise Attendance Chart Card -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Grade-wise Attendance</h5>
                        <canvas id="gradeAttendanceChart" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Payment Status</h5>
                        <canvas id="paymentStatusChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Student Modal (Reusing from students.blade.php) -->
    <div class="modal fade" id="viewStudentModal" tabindex="-1" aria-labelledby="viewStudentModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewStudentModalLabel">View Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mt-3">
                        <label><strong>QR Code:</strong></label>
                        <div id="qr_code" class="d-flex justify-content-center align-items-center my-3"></div>
                    </div>
                    <div class="form-group">
                        <label><strong>Student Number:</strong></label>
                        <p id="view_tcbt_student_number"></p>
                    </div>
                    <div class="form-group">
                        <label><strong>Name:</strong></label>
                        <p id="view_name"></p>
                    </div>
                    <div class="form-group">
                        <label><strong>Contact No:</strong></label>
                        <p id="view_contact_no"></p>
                    </div>
                    <div class="form-group mt-2">
                        <label><strong>Payable Amount:</strong></label>
                        <p id="view_need_to_pay"></p>
                    </div>
                    <!-- Registration Date -->
                    <div class="form-group mt-2">
                        <label><strong>Registration Date:</strong></label>
                        <p id="view_registration_date"></p>
                    </div>
                    <div class="form-group">
                        <label><strong>Grade:</strong></label>
                        <p id="view_grade"></p>
                    </div>
                    <div class="form-group">
                        <label><strong>School:</strong></label>
                        <p id="view_school"></p>
                    </div>
                    <div class="form-group">
                        <label><strong>Address:</strong></label>
                        <p id="view_address"></p>
                    </div>
                    <div class="form-group">
                        <label><strong>Parent Contact No:</strong></label>
                        <p id="view_parent_contact_no"></p>
                    </div>
                    <div class="form-group">
                        <label><strong>Parent Name:</strong></label>
                        <p id="view_parent_name"></p>
                    </div>
                    <div class="form-group">
                        <label><strong>Status:</strong></label>
                        <p id="view_status"></p>
                    </div>
                    <div class="form-group">
                        <label><strong>Payments:</strong></label>
                        <div id="view_payments"></div>
                    </div>
                    <!-- Attendance History Section -->
                    <div class="form-group mt-3">
                        <label><strong>Attendance History:</strong></label>
                        <div id="attendance_history"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function () {
            // Initialize DataTable
            $('#attendanceTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'copy',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    },
                    {
                        extend: 'csv',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    },
                    {
                        extend: 'excel',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    },
                    {
                        extend: 'pdf',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    },
                    {
                        extend: 'print',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    }
                ]
            });

            // Initialize date picker with current date
            if (!$('#attendanceDate').val()) {
                $('#attendanceDate').val(new Date().toISOString().split('T')[0]);
            }

            // Handle date and filter changes
            $('#attendanceFilterForm').submit(function (e) {
                e.preventDefault();
                filterAttendance();
            });

            // Mark attendance function
            $('.mark-attendance').click(function () {
                const studentId = $(this).data('student-id');
                const status = $(this).data('status');
                const date = $('#attendanceDate').val();

                markAttendance(studentId, date, status);
            });

            // Bulk action handlers
            $('#markAllPresent').click(function () {
                if (confirm('Are you sure you want to mark all students as present?')) {
                    bulkMarkAttendance('present');
                }
            });

            $('#markAllAbsent').click(function () {
                if (confirm('Are you sure you want to mark all students as absent?')) {
                    bulkMarkAttendance('absent');
                }
            });

            $('#clearAttendance').click(function () {
                if (confirm('Are you sure you want to clear all attendance records for this date?')) {
                    bulkMarkAttendance('clear');
                }
            });

            $('#printAttendance').click(function () {
                window.print();
            });

            // Initialize Charts
            initializeCharts();
        });

        function filterAttendance() {
            const date = $('#attendanceDate').val();
            const status = $('#filterStatus').val();
            const grade = $('#filterGrade').val();

            window.location.href = `{{ route('attendance.index') }}?date=${date}&status=${status}&grade=${grade}`;
        }

        function markAttendance(studentId, date, status) {
            $.ajax({
                url: '{{ route('attendance.mark') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    student_id: studentId,
                    date: date,
                    status: status
                },
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: response.message,
                            icon: 'success',
                            timer: 2000
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function (xhr) {
                    Swal.fire('Error', 'Failed to mark attendance', 'error');
                }
            });
        }

        function bulkMarkAttendance(action) {
            const date = $('#attendanceDate').val();
            const grade = $('#filterGrade').val();

            $.ajax({
                url: '{{ route('attendance.bulk') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    date: date,
                    action: action,
                    grade: grade
                },
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: response.message,
                            icon: 'success',
                            timer: 2000
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function (xhr) {
                    Swal.fire('Error', 'Failed to perform bulk action', 'error');
                }
            });
        }

        function viewStudent(studentId) {
            $.ajax({
                url: '/students/' + studentId,
                method: 'GET',
                success: function (response) {
                    const student = response.student;

                    $('#view_tcbt_student_number').text(student.tcbt_student_number);
                    $('#view_name').text(student.name);
                    $('#view_contact_no').text(student.contact_no);
                    $('#view_grade').text(student.grade);
                    $('#view_school').text(student.school);
                    $('#view_address').text(student.address);
                    $('#view_parent_contact_no').text(student.parent_contact_no);
                    $('#view_parent_name').text(student.parent_name);
                    $('#view_status').text(student.status == 1 ? 'Active' : 'Inactive');
                    $('#view_need_to_pay').text('Rs. ' + student.need_to_pay);
                    $('#view_registration_date').text(new Date(student.created_at).toLocaleDateString('en-GB'));

                    $('#qr_code').empty(); // Clear any existing QR code
                    new QRCode(document.getElementById('qr_code'), {
                        text: student.tcbt_student_number,
                        width: 128,
                        height: 128,
                    });

                    // Display payments
                    const payments = response.payments;
                    let paymentsHtml = '';

                    if (payments.length > 0) {
                        paymentsHtml = `
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Month/Year</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>`;
                        payments.forEach(payment => {
                            const formattedDate = new Date(payment.created_at).toLocaleDateString('en-GB');
                            paymentsHtml +=
                                `<tr>
                                    <td>Rs. ${payment.amount}</td>
                                    <td>${formattedDate}</td>
                                    <td>${payment.paid_month}/${payment.paid_year}</td>
                                    <td>${payment.status === 'pending' ? '<span class="badge text-bg-warning">Pending</span>' : payment.status === 'completed' ? '<span class="badge text-bg-success">Completed</span>' : '<span class="badge text-bg-danger">Failed</span>'}</td>
                                </tr>`;
                        });

                        paymentsHtml += `
                                </tbody>
                            </table>
                        </div>`;
                    } else {
                        paymentsHtml = '<p>No payments available.</p>';
                    }

                    $('#view_payments').html(paymentsHtml);

                    // Fetch and display attendance history
                    fetchAttendanceHistory(studentId);

                    $('#viewStudentModal').modal('show');
                },
                error: function (xhr) {
                    Swal.fire('Error', 'Failed to fetch student data', 'error');
                }
            });
        }

        function fetchAttendanceHistory(studentId) {
            $.ajax({
                url: '{{ route('attendance.history') }}',
                method: 'GET',
                data: {
                    student_id: studentId
                },
                success: function (response) {
                    if (response.success) {
                        const history = response.history;
                        let historyHtml = '';

                        if (history.length > 0) {
                            historyHtml = `
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Marked By</th>
                                            <th>Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>`;
                            history.forEach(record => {
                                const formattedDate = new Date(record.date).toLocaleDateString('en-GB');
                                historyHtml += `
                                    <tr>
                                        <td>${formattedDate}</td>
                                        <td>${record.status === 'present' ? '<span class="badge text-bg-success">Present</span>' : '<span class="badge text-bg-danger">Absent</span>'}</td>
                                        <td>${record.marked_by_name || 'System'}</td>
                                        <td>${record.created_at ? new Date(record.created_at).toLocaleTimeString() : 'N/A'}</td>
                                    </tr>`;
                            });

                            historyHtml += `
                                    </tbody>
                                </table>
                            </div>`;
                        } else {
                            historyHtml = '<p>No attendance history available.</p>';
                        }

                        $('#attendance_history').html(historyHtml);
                    } else {
                        $('#attendance_history').html('<p>Failed to fetch attendance history.</p>');
                    }
                },
                error: function (xhr) {
                    $('#attendance_history').html('<p>Error loading attendance history.</p>');
                }
            });
        }

        function initializeCharts() {
            // Weekly attendance chart
            const weeklyCtx = document.getElementById('weeklyAttendanceChart').getContext('2d');
            const weeklyLabels = {!! json_encode($weeklyChartData['labels']) !!};
            const weeklyData = {!! json_encode($weeklyChartData['data']) !!};

            new Chart(weeklyCtx, {
                type: 'line',
                data: {
                    labels: weeklyLabels,
                    datasets: [{
                        label: 'Attendance Rate (%)',
                        data: weeklyData,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            title: {
                                display: true,
                                text: 'Attendance Rate (%)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        }
                    }
                }
            });

            // Grade-wise attendance chart
            const gradeCtx = document.getElementById('gradeAttendanceChart').getContext('2d');
            const gradeLabels = {!! json_encode($gradeChartData['labels']) !!};
            const gradePresentData = {!! json_encode($gradeChartData['present']) !!};
            const gradeAbsentData = {!! json_encode($gradeChartData['absent']) !!};

            new Chart(gradeCtx, {
                type: 'bar',
                data: {
                    labels: gradeLabels,
                    datasets: [
                        {
                            label: 'Present',
                            data: gradePresentData,
                            backgroundColor: 'rgba(40, 167, 69, 0.7)'
                        },
                        {
                            label: 'Absent',
                            data: gradeAbsentData,
                            backgroundColor: 'rgba(220, 53, 69, 0.7)'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Students'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Grade'
                            }
                        }
                    }
                }
            });

            // Payment status chart
            const paymentCtx = document.getElementById('paymentStatusChart').getContext('2d');
            const paymentData = {!! json_encode($paymentChartData['data']) !!};

            new Chart(paymentCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Paid', 'Overdue', 'No Payment'],
                    datasets: [{
                        data: paymentData,
                        backgroundColor: [
                            'rgba(40, 167, 69, 0.7)',
                            'rgba(220, 53, 69, 0.7)',
                            'rgba(255, 193, 7, 0.7)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    </script>
@endsection
