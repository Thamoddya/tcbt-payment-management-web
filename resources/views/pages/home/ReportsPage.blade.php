@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <!-- Reports Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="card-title">Reports</h4>
            <p>Generate and download reports for student payments, daily payments, monthly payments, and more.</p>
        </div>
    </div>

    <!-- Report Filters -->
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Filter Reports</h5>
                    <form id="reportForm">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="reportType">Report Type</label>
                            <select class="form-control" id="reportType" name="reportType">
                                <option value="0" selected>Select Payment Type</option>
                                <option value="student_payments">Student Payments</option>
                                <option value="today_payments">Today’s Payments</option>
                                <option value="monthly_payments">Monthly Payments</option>
                                <option value="all_payments">All Payments</option>
                            </select>
                        </div>

                        <!-- Specific Student TCBT Number -->
                        <div class="form-group mb-3" id="studentFilter" style="display: none;">
                            <label for="tcbtStudentNumber">Student TCBT Number</label>
                            <input type="text" class="form-control" id="tcbtStudentNumber" name="tcbtStudentNumber"
                                placeholder="Enter TCBT Number">
                        </div>

                        <!-- Date Picker -->
                        <div class="form-group mb-3" id="dateFilter" style="display: none;">
                            <label for="date">Select Date</label>
                            <input type="date" class="form-control" id="date" name="date">
                        </div>

                        <!-- Month Picker -->
                        <div class="form-group mb-3" id="monthFilter" style="display: none;">
                            <label for="month">Select Month</label>
                            <input type="month" class="form-control" id="month" name="month">
                        </div>

                        <button type="button" class="btn btn-primary" onclick="generateReport()">Generate
                            Report</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Report Table -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Generated Reports</h5>
                    <div class="table-responsive">
                        <table class="table table-striped" id="reportTable">
                            <thead>
                                <tr>
                                    <th>Payment ID</th>
                                    <th>Student Name</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Paid Month</th>
                                    <th>Payment Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Report data will be dynamically inserted here -->
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">
                    Monthly Student Payments
                </h4>

                <!-- Month and Year Selection -->
                <form method="GET" action="{{ route('reports') }}">
                    <div class="form-row">
                        <div class="col mt-2">
                            <select name="month" class="form-control">
                                <option value="">Select Month</option>
                                @foreach (range(1, 12) as $month)
                                    <option value="{{ $month }}" @if(request()->month == $month) selected @endif>
                                        {{ \Carbon\Carbon::create()->month($month)->format('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col mt-2">
                            <select name="year" class="form-control">
                                <option value="">Select Year</option>
                                @foreach (range(2020, \Carbon\Carbon::now()->year) as $year)
                                    <option value="{{ $year }}" @if(request()->year == $year) selected @endif>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col mt-2">
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive mt-4">
                    <table id="dataTable" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Grade</th>
                                <th>Payment Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($filteredStudents as $student)
                                <tr>
                                    <td>{{ $student->tcbt_student_number }}</td>
                                    <td>{{ $student->name }}</td>
                                    <td>{{ $student->grade }}</td>
                                    <td>{{ $student->payment_status }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Grade</th>
                                <th>Payment Status</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        // Initialize DataTable
        $('#reportTable').DataTable();
        $('#dataTable').DataTable(
            {
                dom: 'Bfrtip',
                buttons: [
                    'excel', 'pdf', 'print'
                ]
            }
        );

        $('#reportType').change(function () {
            const reportType = $(this).val();
            $('#studentFilter').hide();
            $('#dateFilter').hide();
            $('#monthFilter').hide();

            if (reportType === 'student_payments') {
                $('#studentFilter').show();
            } else if (reportType === 'today_payments') {
                $('#dateFilter').show();
            } else if (reportType === 'monthly_payments') {
                $('#monthFilter').show();
            }
        });
    });

    function generateReport() {
        const reportType = $('#reportType').val();
        const studentNumber = $('#tcbtStudentNumber').val();
        const date = $('#date').val();
        const month = $('#month').val();

        // Show a loading indicator (optional)
        $('#reportTable tbody').html('<tr><td colspan="6" class="text-center">Loading...</td></tr>');

        $.ajax({
            url: '/reports/generate',
            method: 'POST',
            data: {
                _token: $('input[name="_token"]').val(),
                reportType: reportType,
                tcbtStudentNumber: studentNumber,
                date: date,
                month: month,
            },
            success: function (response) {
                $('#reportTable').DataTable();
                if (response.success) {

                    let reportHtml = '';
                    let totalAmount = 0; // Variable to track the total payment amount
                    response.data.forEach(payment => {
                        totalAmount += parseFloat(payment.amount); // Add to the total amount
                        reportHtml += `
                        <tr>
                            <td>${payment.payment_id}</td>
                            <td>${payment.student.name}</td>
                            <td>${payment.amount}</td>
                            <td>${payment.status}</td>
                            <td>${payment.paid_month}/${payment.paid_year}</td>
                            <td>${new Date(payment.payment_date).toLocaleDateString()}</td>
                        </tr>
                    `;
                    });

                    // Clear old data and destroy the DataTable
                    $('#reportTable').DataTable().clear().destroy();

                    // Insert the new data
                    $('#reportTable tbody').html(reportHtml);

                    // Reinitialize DataTable with Export Buttons
                    $('#reportTable').DataTable({
                        dom: 'Bfrtip',
                        buttons: [{
                            extend: 'excelHtml5',
                            text: 'Download Excel',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'pdfHtml5',
                            text: 'Download PDF',
                            exportOptions: {
                                columns: ':visible'
                            },
                            orientation: 'landscape',
                            pageSize: 'A4',
                            customize: function (doc) {
                                doc.content.push({
                                    text: `\nTotal Amount: ${totalAmount.toFixed(2)}`,
                                    style: 'subheader',
                                    alignment: 'right'
                                });
                            }
                        },
                        {
                            extend: 'print',
                            text: 'Print',
                            exportOptions: {
                                columns: ':visible'
                            },
                            customize: function (win) {
                                $(win.document.body).append(
                                    `<div style="text-align: right; font-weight: bold; margin-top: 20px;">Total Amount: ${totalAmount.toFixed(2)}</div>`
                                );
                            }
                        }
                        ]
                    });
                } else {
                    $('#reportTable tbody').html(
                        '<tr><td colspan="6" class="text-center">No data available</td></tr>'
                    );
                }
            },
            error: function (e) {
                console.log(e);

                alert('Error generating report. Please try again.');
                $('#reportTable tbody').html(
                    '<tr><td colspan="6" class="text-center">Error loading data</td></tr>'
                );
            }
        });
    }
</script>
@endsection