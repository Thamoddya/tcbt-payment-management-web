@extends('layouts.MainLayout')

@section('content')
    <div class="container-fluid">

        <div class="row">
            {{-- Add Student Model Button --}}
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Add Student</h4>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addStudentModel">
                            Add Student
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Students</h4>
                        <div class="table-responsive">
                            <table id="dataTable" class="table table-striped" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Student ID</th>
                                    <th>Name</th>
                                    <th>Grade</th>
                                    <th>status</th>
                                    <th>Contact No</th>
                                    <th>Registered Date</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($allStudents as $student)
                                    <tr>
                                        <td data-class-name="priority">{{ $student->tcbt_student_number }}</td>
                                        <td>{{ $student->name }}</td>
                                        <td>{{ $student->grade }}</td>
                                        <td>
                                            @if ($student->status == 1)
                                                <span class="badge text-bg-secondary">Active</span>
                                            @else
                                                <span class="badge text-bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $student->contact_no }}</td>
                                        <td>{{ $student->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <a href="javascript:void(0)" class="btn btn-success btn-sm my-1"
                                               onclick="viewStudent({{ $student->id }})">View</a>
                                            <a href="javascript:void(0)" class="btn btn-primary btn-sm my-1"
                                               onclick="loadStudentData({{ $student->id }})">Edit</a>
                                            <a href="" class="btn btn-danger btn-sm my-1">Delete</a>

                                            <a href="javascript:void(0)" class="btn btn-info btn-sm my-1"
                                               onclick="openAddPaymentModal('{{ $student->tcbt_student_number }}')">Add
                                                Payment</a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>Student ID</th>
                                    <th>Name</th>
                                    <th>Grade</th>
                                    <th>Office</th>
                                    <th>Contact No</th>
                                    <th>Registered Date</th>
                                    <th>Actions</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- Add Student Modal --}}
        <div class="modal fade" id="addStudentModel" tabindex="-1" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addStudentModalLabel">Add Student</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form id="studentForm" method="POST">
                            @csrf

                            <!-- Name -->
                            <div class="form-group row mt-2">
                                <label for="name" class="col-sm-3 col-form-label">Name</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Name">
                                    <div class="text-danger mt-1" id="error-name"></div>
                                </div>
                            </div>

                            <!-- Contact No -->
                            <div class="form-group row mt-2">
                                <label for="contact_no" class="col-sm-3 col-form-label">Contact No</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="contact_no" name="contact_no"
                                           placeholder="Contact No">
                                    <div class="text-danger mt-1" id="error-contact_no"></div>
                                </div>
                            </div>

                            <!-- Grade -->
                            <div class="form-group row mt-2">
                                <label for="grade" class="col-sm-3 col-form-label">Grade</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="grade" name="grade" placeholder="Grade">
                                    <div class="text-danger mt-1" id="error-grade"></div>
                                </div>
                            </div>

                            <!-- Registration Date -->
                            <div class="form-group row mt-2">
                                <label for="registration_date" class="col-sm-3 col-form-label">Registration Date</label>
                                <div class="col-sm-9">
                                    <input type="date" class="form-control" id="registration_date"
                                           name="registration_date">
                                    <div class="text-danger mt-1" id="error-registration_date"></div>
                                </div>
                            </div>


                            {{-- need_to_pay amount --}}
                            <div class="form-group row mt-2">
                                <label for="need_to_pay" class="col-sm-3 col-form-label">
                                    Payable Amount
                                </label>
                                <div class="col-sm-9">
                                    <input type="number" maxlength="4" class="form-control" id="need_to_pay"
                                           name="need_to_pay" placeholder="Payable Amount">
                                    <div class="text-danger mt-1" id="error-need_to_pay"></div>
                                </div>
                            </div>

                            <!-- School -->
                            <div class="form-group row mt-2">
                                <label for="school" class="col-sm-3 col-form-label">School</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="school" name="school"
                                           placeholder="School">
                                    <div class="text-danger mt-1" id="error-school"></div>
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="form-group row mt-2">
                                <label for="address" class="col-sm-3 col-form-label">Address</label>
                                <div class="col-sm-9">
                                <textarea class="form-control" id="address" name="address" placeholder="Address"
                                          rows="2"></textarea>
                                    <div class="text-danger mt-1" id="error-address"></div>
                                </div>
                            </div>

                            <!-- Parent Contact No -->
                            <div class="form-group row mt-2">
                                <label for="parent_contact_no" class="col-sm-3 col-form-label">Parent Contact
                                    No</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="parent_contact_no"
                                           name="parent_contact_no"
                                           placeholder="Parent Contact No">
                                    <div class="text-danger mt-1" id="error-parent_contact_no"></div>
                                </div>
                            </div>

                            <!-- Parent Name -->
                            <div class="form-group row mt-2">
                                <label for="parent_name" class="col-sm-3 col-form-label">Parent Name</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="parent_name" name="parent_name"
                                           placeholder="Parent Name">
                                    <div class="text-danger mt-1" id="error-parent_name"></div>
                                </div>
                            </div>

                            <div class="form-group row mt-3">
                                <div class="col-sm-12">
                                    <button type="button" onclick="addStudent()" class="btn btn-primary">Add
                                        Student
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- View Student Modal --}}

        <!-- View Student Modal -->
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
                            <label><strong>Payble Amount:</strong></label>
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
                            <ul id="view_payments"></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Update Student Modal --}}

        <div class="modal fade" id="editStudentModal" tabindex="-1" aria-labelledby="editStudentModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editStudentModalLabel">Edit Student</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editStudentForm">
                            @csrf
                            @method('PUT')

                            <!-- Student Number (Read-only) -->
                            <div class="form-group">
                                <label for="edit_tcbt_student_number"><strong>Student Number:</strong></label>
                                <input type="text" class="form-control" id="edit_tcbt_student_number"
                                       name="tcbt_student_number" readonly>
                            </div>

                            <!-- Name -->
                            <div class="form-group mt-2">
                                <label for="edit_name"><strong>Name:</strong></label>
                                <input type="text" class="form-control" id="edit_name" name="name">
                            </div>

                            <!-- Contact No -->
                            <div class="form-group mt-2">
                                <label for="edit_contact_no"><strong>Contact No:</strong></label>
                                <input type="text" class="form-control" id="edit_contact_no" name="contact_no">
                            </div>

                            {{-- need_to_pay --}}
                            <div class="form-group mt-2">
                                <label for="edit_need_to_pay"><strong>Payable Amount:</strong></label>
                                <input type="number" class="form-control" id="edit_need_to_pay" name="need_to_pay">
                            </div>

                            <!-- Registration Date -->
                            <div class="form-group mt-2">
                                <label for="edit_registration_date"><strong>Registration Date:</strong></label>
                                <input type="date" class="form-control" id="edit_registration_date"
                                       name="registration_date">
                            </div>


                            <!-- Grade -->
                            <div class="form-group mt-2">
                                <label for="edit_grade"><strong>Grade:</strong></label>
                                <input type="text" class="form-control" id="edit_grade" name="grade">
                            </div>

                            <!-- School -->
                            <div class="form-group mt-2">
                                <label for="edit_school"><strong>School:</strong></label>
                                <input type="text" class="form-control" id="edit_school" name="school">
                            </div>

                            <!-- Address -->
                            <div class="form-group mt-2">
                                <label for="edit_address"><strong>Address:</strong></label>
                                <textarea class="form-control" id="edit_address" name="address" rows="2"></textarea>
                            </div>

                            <!-- Parent Contact No -->
                            <div class="form-group mt-2">
                                <label for="edit_parent_contact_no"><strong>Parent Contact No:</strong></label>
                                <input type="text" class="form-control" id="edit_parent_contact_no"
                                       name="parent_contact_no">
                            </div>

                            <!-- Parent Name -->
                            <div class="form-group mt-2">
                                <label for="edit_parent_name"><strong>Parent Name:</strong></label>
                                <input type="text" class="form-control" id="edit_parent_name" name="parent_name">
                            </div>

                            <!-- Status -->
                            <div class="form-group mt-2">
                                <label for="edit_status"><strong>Status:</strong></label>
                                <select class="form-control" id="edit_status" name="status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>

                            <!-- Update Button -->
                            <div class="form-group mt-3">
                                <button type="button" onclick="updateStudent()" class="btn btn-primary">Update
                                    Student
                                </button>
                            </div>
                        </form>
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

                            <input type="hidden" id="paymentStudentId">

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

        $(document).ready(function () {
            $('#addPaymentModal').on('show.bs.modal', function () {
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
            $('#addPaymentModal').on('show.bs.modal', function () {
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

        $(document).ready(function () {
            $('#dataTable').DataTable({
                dom: 'Bfrtip',
                buttons: [{
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
        });


        function addStudent() {
            const studentData = {
                _token: '{{ csrf_token() }}',
                tcbt_student_number: $('#tcbt_student_number').val(),
                name: $('#name').val(),
                contact_no: $('#contact_no').val(),
                grade: $('#grade').val(),
                school: $('#school').val(),
                address: $('#address').val(),
                parent_contact_no: $('#parent_contact_no').val(),
                parent_name: $('#parent_name').val(),
                need_to_pay: $('#need_to_pay').val(),
                registration_date: $('#registration_date').val()
            };

            $('.text-danger').text('');

            $.ajax({
                url: '{{ route('students.store') }}',
                method: 'POST',
                data: studentData,
                success: function (response) {
                    alert(response.success);
                    $('#studentForm')[0].reset();
                    $('#addStudentModel').modal('hide');
                    window.location.reload();
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        for (const field in errors) {
                            $(`#error-${field}`).text(errors[field][0]);
                        }
                    }
                }
            });
        }

        function viewStudent(studentId) {
            $.ajax({
                url: '/students/' + studentId,
                method: 'GET',
                success: function (response) {
                    const student = response.student;
                    console.log(student);

                    $('#view_tcbt_student_number').text(student.tcbt_student_number);
                    $('#view_name').text(student.name);
                    $('#view_contact_no').text(student.contact_no);
                    $('#view_grade').text(student.grade);
                    $('#view_school').text(student.school);
                    $('#view_address').text(student.address);
                    $('#view_parent_contact_no').text(student.parent_contact_no);
                    $('#view_parent_name').text(student.parent_name);
                    $('#view_status').text(student.status == 1 ? 'Active' : 'Inactive');
                    $('#view_need_to_pay').text(student.need_to_pay);
                    $('#view_registration_date').text(new Date(student.created_at).toLocaleDateString('en-GB'));

                    $('#qr_code').empty(); // Clear any existing QR code
                    new QRCode(document.getElementById('qr_code'), {
                        text: student.tcbt_student_number,
                        width: 128,
                        height: 128,
                    });

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
                            const formattedDate = new Date(payment.created_at).toLocaleDateString(
                                'en-GB');
                            paymentsHtml +=
                                `<tr>
                                <td>${payment.amount}</td>
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

                    $('#viewStudentModal').modal('show');
                },
                error: function (xhr) {
                    alert('Error fetching student data');
                }
            });
        }

        function loadStudentData(studentId) {
            $.ajax({
                url: '/students/' + studentId,
                method: 'GET',
                success: function (response) {
                    const student = response.student;

                    console.log(student);


                    $('#edit_tcbt_student_number').val(student.tcbt_student_number);
                    $('#edit_name').val(student.name);
                    $('#edit_contact_no').val(student.contact_no);
                    $('#edit_grade').val(student.grade);
                    $('#edit_school').val(student.school);
                    $('#edit_address').val(student.address);
                    $('#edit_parent_contact_no').val(student.parent_contact_no);
                    $('#edit_parent_name').val(student.parent_name);
                    $('#edit_status').val(student.status);
                    $('#edit_need_to_pay').val(student.need_to_pay);
                    $('#edit_registration_date').val(student.registration_date);

                    $('#editStudentModal').modal('show');
                },
                error: function (xhr) {
                    alert('Error fetching student data');
                }
            });
        }

        function updateStudent() {
            const studentId = $('#edit_tcbt_student_number').val();

            const formData = {
                _token: '{{ csrf_token() }}',
                _method: 'POST',
                name: $('#edit_name').val(),
                contact_no: $('#edit_contact_no').val(),
                grade: $('#edit_grade').val(),
                school: $('#edit_school').val(),
                address: $('#edit_address').val(),
                parent_contact_no: $('#edit_parent_contact_no').val(),
                parent_name: $('#edit_parent_name').val(),
                status: $('#edit_status').val(),
                tcbt_student_number: studentId,
                need_to_pay: $('#edit_need_to_pay').val(),
                registration_date: $('#edit_registration_date').val()
            };

            $.ajax({
                url: 'student/update',
                method: 'POST',
                data: formData,
                success: function (response) {
                    swal.fire({
                        title: 'Success',
                        text: response.success,
                        icon: 'success',
                        timer: 2000
                    });
                    $('#editStudentModal').modal('hide');
                    location.reload();
                },
                error: function (xhr) {
                    const errors = xhr.responseJSON.errors;
                    let errorMessage = 'Error updating student: ';
                    for (const key in errors) {
                        errorMessage += errors[key][0] + ' ';
                    }
                    alert(errorMessage);
                }
            });
        }

        function openAddPaymentModal(studentId) {
            $('#addPaymentModal').modal('show');
            $('#paymentStudentId').val(studentId);
        }

        function addPayment() {
            const studentsId = $('#paymentStudentId').val();
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
                success: function (response) {
                    if (response.success) {
                        swal.fire('Payment and invoice added successfully!');
                        $('#addPaymentModal').modal('hide');

                    } else if (response.success == "false") {
                        swal.fire('Failed to add payment: ' + (response.errors ? JSON.stringify(response
                                .errors) :
                            response.message));
                    } else {
                        swal.fire('Failed to add payment: ' + (response.errors ? JSON.stringify(response
                                .errors) :
                            response.message));
                    }
                },
                error: function (xhr) {
                    if (xhr.status == 422) {
                        swal.fire('Failed to add payment: ' + JSON.stringify(xhr.responseJSON.message));
                    } else {
                        swal.fire('Failed to add payment: ' + xhr.responseJSON.message);

                    }
                }
            });
        }
    </script>
@endsection
