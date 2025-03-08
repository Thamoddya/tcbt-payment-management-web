@extends('layouts.MainLayout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            {{-- Add Cashier Model Button --}}
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Book Management</h4>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCashierModel">
                            Add Cashier
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Cashier List</h5>
                        <div class="table-responsive">
                            <table id="dataTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Name</th>
                                        <th>NIC</th>
                                        <th>Email</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cashiers as $index => $cashier)
                                        <tr>
                                            <td>{{ $index }}</td>
                                            <td>{{ $cashier->name }}</td>
                                            <td>{{ $cashier->nic }}</td>
                                            <td>{{ $cashier->email }}</td>
                                            <td>{{ $cashier->created_at }}</td>
                                            <td>{{ $cashier->updated_at }}</td>
                                            <td>
                                                <a href="javascript:void(0)" class="btn btn-primary btn-sm my-1"
                                                    onclick="loadCashierData({{ $cashier->id }})">Edit</a>
                                                <a href="" class="btn btn-danger btn-sm">Delete</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Id</th>
                                        <th>Name</th>
                                        <th>NIC</th>
                                        <th>Email</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Cashier Modal -->
        <div class="modal fade" id="addCashierModel" tabindex="-1" aria-labelledby="addCashierLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="addCashierForm">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="addCashierLabel">Add Cashier</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="nic" class="form-label">NIC</label>
                                <input type="text" class="form-control" id="nic" name="nic" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Update Cashier Modal --}}
        <div class="modal fade" id="updateCashierModel" tabindex="-1" aria-labelledby="updateCashierLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="updateCashierForm">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="updateCashierLabel">Update Cashier</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="update-name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="update-email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="update-nic" class="form-label">NIC</label>
                                <input type="text" class="form-control" id="nic" name="nic" required>
                            </div>
                            <div class="mb-3">
                                <label for="update-password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
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
        $('#addCashierForm').submit(function(e) {
            e.preventDefault();

            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            var formData = {
                name: $('#name').val(),
                email: $('#email').val(),
                nic: $('#nic').val(),
                password: $('#password').val(),
                _token: "{{ csrf_token() }}",
            };

            $.ajax({
                url: "{{ route('add.cashier') }}",
                type: "POST",
                data: formData,
                success: function(response) {
                    if (response.status == 200) {
                        alert(response.message);
                        location.reload(); // Reload page
                    } else {
                        alert('Failed to add cashier');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) { // Validation error
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            var inputField = $('#' + key);
                            inputField.addClass('is-invalid');

                            inputField.after('<div class="invalid-feedback">' + value[0] +
                                '</div>');
                        });
                    } else {
                        alert('An unexpected error occurred');
                    }
                }
            });
        });

        function loadCashierData(id) {
            $.ajax({
                url: "{{ url('/get-cashier/') }}/" + id,
                type: "GET",
                success: function(response) {
                    if (response.status == 200) {
                        var cashier = response.cashier;
                        $('#updateCashierForm #name').val(cashier.name);
                        $('#updateCashierForm #email').val(cashier.email);
                        $('#updateCashierForm #nic').val(cashier.nic);
                        $('#updateCashierForm #password').val('');

                        $('#updateCashierForm').attr('action', "{{ url('/update-cashier/') }}/" + id);
                        $('#updateCashierModel').modal('show');
                    } else {
                        alert('Failed to load cashier data');
                    }
                },
                error: function(xhr) {
                    alert('An unexpected error occurred');
                }
            });
        }

        $('#updateCashierForm').submit(function(e) {
            e.preventDefault();

            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            var formData = {
                name: $('#updateCashierForm #name').val(),
                email: $('#updateCashierForm #email').val(),
                nic: $('#updateCashierForm #nic').val(),
                password: $('#updateCashierForm #password').val(),
                _token: "{{ csrf_token() }}",
            };

            var id = $('#updateCashierForm').attr('action').split('/').pop();

            $.ajax({
                url: "{{ url('/update-cashier/') }}/" + id,
                type: "POST",
                data: formData,
                success: function(response) {
                    if (response.status == 200) {
                        alert(response.message);
                        location.reload(); // Reload page
                    } else {
                        alert('Failed to update cashier');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) { // Validation error
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            var inputField = $('#updateCashierForm #' + key);
                            inputField.addClass('is-invalid');
                            inputField.after('<div class="invalid-feedback">' + value[0] +
                                '</div>');
                        });
                    } else {
                        alert('An unexpected error occurred');
                    }
                }
            });
        });
    </script>
@endsection
