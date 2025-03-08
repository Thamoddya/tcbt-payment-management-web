@extends('layouts.MainLayout')

@section('content')
    <div class="container">
        <h2 class="my-4">Library Book Borrowing</h2>

        {{--ERRORS AND SUCCESS--}}
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Borrow Book Form -->
        <form action="{{ route('library.borrow') }}" method="POST">
            @csrf
            <div class="row mb-3">
                <div class="col-md-4">
                    <label>Book ID</label>
                    <input type="text" name="book_id" class="form-control" required autofocus
                           placeholder="Scan QR Code">
                </div>
                <div class="col-md-4">
                    <label>Student TCBT Number</label>
                    <input type="text" name="student_tcbt_number" class="form-control" required
                           placeholder="Enter TCBT Number">
                </div>
                <div class="col-md-4">
                    <label>Borrow Date</label>
                    <input type="date" name="borrowed_at" class="form-control" value="{{ date('Y-m-d') }}">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Borrow</button>
        </form>

        <hr>
        <h3>Borrowed Books</h3>
        <table class="table table-bordered" id="dataTable">
            <thead>
            <tr>
                <th>Book ID</th>
                <th>Title</th>
                <th>Student</th>
                <th>Borrowed Date</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($borrowedBooks as $borrow)
                <tr>
                    <td>{{ $borrow->book->book_id }}</td>
                    <td>{{ $borrow->book->title }}</td>
                    <td>{{ $borrow->student->name }}</td>
                    <td>{{ $borrow->borrowed_at}}</td>
                    <td>
                        <form action="{{ route('library.return', $borrow->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-success">Return</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <hr>
        <h3>Overdue Books (More than 14 days)</h3>
        <table class="table table-bordered" id="dataTable">
            <thead>
            <tr>
                <th>Book ID</th>
                <th>Title</th>
                <th>Student</th>
                <th>Borrowed Date</th>
                <th>Days Overdue</th>
            </tr>
            </thead>
            <tbody>
            @foreach($overdueBooks as $overdue)
                <tr>
                    <td>{{ $overdue->book->book_id }}</td>
                    <td>{{ $overdue->book->title }}</td>
                    <td>{{ $overdue->student->name }}</td>
                    <td>{{ $overdue->borrowed_at }}</td>
                    <td>{{ now()->diffInDays($overdue->borrowed_at) - 14 }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            $('#dataTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {extend: 'copy'},
                    {extend: 'csv'},
                    {extend: 'excel'},
                    {extend: 'pdf'},
                    {extend: 'print'}
                ]
            });
        });
    </script>
@endsection
