@extends('layouts.MainLayout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Search Student by TCBT Number</h4>

                        <!-- Input for TCBT Student Number -->
                        <div class="mb-4">
                            <input {{-- Auto Focus --}} autofocus type="text" id="tcbt_student_number" class="form-control"
                                placeholder="Enter TCBT Student Number" onkeyup="fetchStudentDetails()">
                            <button type="button" class="btn btn-primary mt-2"
                                onclick="fetchStudentDetails()">Search</button>
                        </div>

                        <!-- Student Details -->
                        <div id="studentDetails" class="mb-4" style="display: none;">
                            <h5>Student Details</h5>
                            <p><strong>Name:</strong> <span id="student_name"></span></p>
                            <p><strong>Grade:</strong> <span id="student_grade"></span></p>
                            <p><strong>School:</strong> <span id="student_school"></span></p>
                            <p><strong>Contact No:</strong> <span id="student_contact_no"></span></p>
                            <p><strong>Status:</strong> <span id="student_status"></span></p>
                        </div>

                        <!-- Payments Table -->
                        <div id="paymentsSection" style="display: none;">
                            <h5>Past Payments</h5>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Month/Year</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="paymentsTableBody">
                                    <!-- Filled dynamically by JS -->
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function fetchStudentDetails() {
            const tcbtStudentNumber = $('#tcbt_student_number').val().trim();

            if (!tcbtStudentNumber) {
                // alert('Please enter a TCBT Student Number.');
                return;
            }

            $.ajax({
                url: `/students/details/${tcbtStudentNumber}`,
                method: 'GET',
                success: function(response) {
                    if (response.student) {
                        // Display student details
                        $('#student_name').text(response.student.name);
                        $('#student_grade').text(response.student.grade);
                        $('#student_school').text(response.student.school);
                        $('#student_contact_no').text(response.student.contact_no);
                        $('#student_status').text(response.student.status == 1 ? 'Active' : 'Inactive');
                        $('#studentDetails').show();

                        // Display payments table
                        const payments = response.payments;
                        let paymentsHtml = '';

                        if (payments.length > 0) {
                            payments.forEach(payment => {
                                const formattedDate = new Date(payment.created_at)
                                    .toLocaleDateString('en-GB');
                                paymentsHtml += `
                                <tr>
                                    <td>${payment.amount}</td>
                                    <td>${formattedDate}</td>
                                    <td>${payment.paid_month}/${payment.paid_year}</td>
                                    <td>
                                        ${
                                            payment.status === 'pending'
                                            ? '<span class="badge text-bg-warning">Pending</span>'
                                            : payment.status === 'completed'
                                            ? '<span class="badge text-bg-success">Completed</span>'
                                            : '<span class="badge text-bg-danger">Failed</span>'
                                        }
                                    </td>
                                </tr>
                            `;
                            });

                            $('#paymentsTableBody').html(paymentsHtml);
                            $('#paymentsSection').show();
                        } else {
                            $('#paymentsTableBody').html(
                                '<tr><td colspan="4" class="text-center">No payments available.</td></tr>'
                            );
                            $('#paymentsSection').show();
                        }
                    } else {
                        alert('No student found with this TCBT number.');
                        $('#studentDetails').hide();
                        $('#paymentsSection').hide();
                    }
                },
                error: function() {
                    $('#studentDetails').hide();
                    $('#paymentsSection').hide();
                }
            });
        }
    </script>
@endsection
