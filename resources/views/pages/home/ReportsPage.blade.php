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
                        <button id="downloadReportBtn" class="btn btn-success mt-3" style="display: none;">Download
                            Report</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#reportType').change(function() {
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
                success: function(response) {
                    if (response.success) {
                        console.log(response.data);

                        let reportHtml = '';
                        response.data.forEach(payment => {
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
                        $('#reportTable tbody').html(reportHtml);
                        $('#downloadReportBtn').show().off('click').on('click', function() {
                            window.location.href = response.download_url;
                        });
                    } else {
                        // alert('No data found for the selected filters.');
                        $('#reportTable tbody').html(
                            '<tr><td colspan="6" class="text-center">No data available</td></tr>');
                        $('#downloadReportBtn').hide();
                    }
                },
                error: function() {
                    alert('Error generating report. Please try again.');
                    $('#reportTable tbody').html(
                        '<tr><td colspan="6" class="text-center">Error loading data</td></tr>');
                    $('#downloadReportBtn').hide();
                }
            });
        }
    </script>
@endsection
