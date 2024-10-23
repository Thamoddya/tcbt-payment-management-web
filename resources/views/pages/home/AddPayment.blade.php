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
                            <div class="input-group">
                                <input autofocus type="text" id="tcbt_student_number" class="form-control"
                                    placeholder="Enter TCBT Student Number">
                                <button type="button" class="btn btn-primary"
                                    onclick="fetchStudentDetails()">Search</button>
                                <button type="button" class="btn btn-secondary" onclick="clearInput()">Clear</button>
                            </div>
                        </div>

                        <!-- Loading Indicator -->
                        <div id="loadingIndicator" class="mb-4" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <span>Loading, please wait...</span>
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
                            {{-- Add Payment Button --}}
                            <div class="mb-4">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#addPaymentModal">
                                    Add Payment To Student
                                </button>
                            </div>
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
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        {{-- Add Student Payment Model --}}
        <div class="modal fade" id="addPaymentModal" tabindex="-1" aria-labelledby="addPaymentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addPaymentModalLabel">Add Payment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="addPaymentForm">
                            <div class="mb-3">
                                <label for="paymentAmount" class="form-label">Amount</label>
                                <input type="number" class="form-control" id="paymentAmount" required>
                            </div>
                            <div class="mb-3">
                                <label for="paidMonth" class="form-label">Paid Month</label>
                                <select id="paidMonth" class="form-control" required>
                                    <option value="">Select Month</option>
                                    @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                        <option value="{{ $month }}">{{ $month }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="paidYear" class="form-label">Paid Year</label>
                                <select id="paidYear" class="form-control" required>
                                    <option value="">Select Year</option>
                                    @foreach (['2023', '2024', '2025', '2026'] as $year)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="paymentStatus" class="form-label">Status</label>
                                <select id="paymentStatus" class="form-control" required>
                                    <option value="pending">Pending</option>
                                    <option value="completed">Completed</option>
                                    <option value="failed">Failed</option>
                                </select>
                            </div>
                            <button onclick="addPayment()" type="submit" class="btn btn-primary">Add Payment</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#tcbt_student_number').focus();

            $('#tcbt_student_number').on('keyup', debounce(fetchStudentDetails, 300));

            $('#addPaymentModal').on('show.bs.modal', function() {
                const today = new Date();
                const currentMonth = today.toLocaleString('default', {
                    month: 'long'
                });
                const currentYear = today.getFullYear();

                $('#paidMonth').val(currentMonth);
                $('#paidYear').val(currentYear);
                $('#paymentStatus').val('completed');

                const formattedDate = today.toISOString().split('T')[0];
                $('#paymentDate').val(formattedDate);
            });
        });

        function debounce(func, delay) {
            let debounceTimer;
            return function() {
                const context = this;
                const args = arguments;
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => func.apply(context, args), delay);
            };
        }

        function fetchStudentDetails() {
            const tcbtStudentNumber = $('#tcbt_student_number').val().trim();

            if (!tcbtStudentNumber) {
                $('#studentDetails').hide();
                $('#paymentsSection').hide();
                return;
            }

            $('#loadingIndicator').show();

            $.ajax({
                url: `/students/details/` + encodeURIComponent(tcbtStudentNumber),
                method: 'GET',
                success: function(response) {
                    $('#loadingIndicator').hide();

                    if (response.student) {
                        $('#student_name').text(response.student.name);
                        $('#student_grade').text(response.student.grade);
                        $('#student_school').text(response.student.school);
                        $('#student_contact_no').text(response.student.contact_no);
                        $('#student_status').text(response.student.status == 1 ? 'Active' : 'Inactive');
                        $('#studentDetails').show();

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
                                </tr>`;
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
                    $('#loadingIndicator').hide();
                    $('#studentDetails').hide();
                    $('#paymentsSection').hide();
                }
            });
        }

        function addPayment() {
            const studentsId = $('#tcbt_student_number').val().trim();
            const amount = $('#paymentAmount').val();
            const paidMonth = $('#paidMonth').val();
            const paidYear = $('#paidYear').val();
            const status = $('#paymentStatus').val();

            if (!studentsId || !amount || !paidMonth || !paidYear || !status) {
                alert('Please fill all required fields.');
                return;
            }

            const paymentData = {
                _token: '{{ csrf_token() }}',
                students_id: studentsId,
                amount: amount,
                paid_month: paidMonth,
                paid_year: paidYear,
                status: status,
            };

            console.log(paymentData);


            $.ajax({
                url: '{{ route('payments.store') }}',
                method: 'POST',
                data: paymentData,
                success: function(response) {
                    if (response.success) {
                        swal.fire('Payment and invoice added successfully!');
                        $('#addPaymentModal').modal('hide');
                        fetchStudentDetails();
                    } else {
                        swal.fire('Failed to add payment: ' + (response.errors ? JSON.stringify(response
                                .errors) :
                            response.message));
                    }
                },
                error: function(xhr) {
                    alert('Error adding payment: ' + xhr.responseText);
                }
            });
        }


        function clearInput() {
            $('#tcbt_student_number').val('').focus();
            $('#studentDetails').hide();
            $('#paymentsSection').hide();
            $('#loadingIndicator').hide();
        }
    </script>
@endsection
