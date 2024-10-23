@extends('layouts.MainLayout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            {{-- Add Student Model Button --}}
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Add Student</h4>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModel">
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
                                            <td>{{ $student->tcbt_student_number }}</td>
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
        <div class="modal fade" id="addStudentModel" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Name">
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
                                    <input type="text" class="form-control" id="grade" name="grade"
                                        placeholder="Grade">
                                    <div class="text-danger mt-1" id="error-grade"></div>
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
                                    <textarea class="form-control" id="address" name="address" placeholder="Address" rows="2"></textarea>
                                    <div class="text-danger mt-1" id="error-address"></div>
                                </div>
                            </div>

                            <!-- Parent Contact No -->
                            <div class="form-group row mt-2">
                                <label for="parent_contact_no" class="col-sm-3 col-form-label">Parent Contact No</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="parent_contact_no"
                                        name="parent_contact_no" placeholder="Parent Contact No">
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
                                        Student</button>
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
                                    Student</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script>
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
            };

            $('.text-danger').text('');

            $.ajax({
                url: '{{ route('students.store') }}',
                method: 'POST',
                data: studentData,
                success: function(response) {
                    alert(response.success);
                    $('#studentForm')[0].reset();
                    $('#addStudentModel').modal('hide');
                },
                error: function(xhr) {
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
                success: function(response) {
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

                    $('#qr_code').empty(); // Clear any existing QR code
                    new QRCode(document.getElementById('qr_code'), {
                        text: student.tcbt_student_number,
                        width: 128,
                        height: 128,
                    });
                    const payments = response.payments;
                    let paymentsHtml = '';
                    if (payments.length > 0) {
                        payments.forEach(payment => {
                            const formattedDate = new Date(payment.created_at).toLocaleDateString(
                                'en-GB');
                            paymentsHtml += `
                        <li>Amount: ${payment.amount}, Date: ${formattedDate} For ${payment.paid_month}/${payment.paid_year}</li>
                    `;
                        });
                    } else {
                        paymentsHtml = '<li>No payments available.</li>';
                    }
                    $('#view_payments').html(paymentsHtml);

                    $('#viewStudentModal').modal('show');
                },
                error: function(xhr) {
                    alert('Error fetching student data');
                }
            });
        }

        function loadStudentData(studentId) {
            $.ajax({
                url: '/students/' + studentId,
                method: 'GET',
                success: function(response) {
                    const student = response.student;

                    $('#edit_tcbt_student_number').val(student.tcbt_student_number);
                    $('#edit_name').val(student.name);
                    $('#edit_contact_no').val(student.contact_no);
                    $('#edit_grade').val(student.grade);
                    $('#edit_school').val(student.school);
                    $('#edit_address').val(student.address);
                    $('#edit_parent_contact_no').val(student.parent_contact_no);
                    $('#edit_parent_name').val(student.parent_name);
                    $('#edit_status').val(student.status);

                    $('#editStudentModal').modal('show');
                },
                error: function(xhr) {
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
                tcbt_student_number: studentId
            };

            $.ajax({
                url: 'student/update',
                method: 'POST',
                data: formData,
                success: function(response) {
                    swal.fire({
                        title: 'Success',
                        text: response.success,
                        icon: 'success',
                        timer: 2000
                    });
                    $('#editStudentModal').modal('hide');
                    location.reload();
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON.errors;
                    let errorMessage = 'Error updating student: ';
                    for (const key in errors) {
                        errorMessage += errors[key][0] + ' ';
                    }
                    alert(errorMessage);
                }
            });
        }
    </script>
@endsection
