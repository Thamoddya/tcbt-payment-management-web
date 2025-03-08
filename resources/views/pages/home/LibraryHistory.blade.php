@extends('layouts.MainLayout')

@section('content')
    <div class="container">
        <h2 class="mb-4">Returned Books History</h2>
        <table class="table table-bordered" id="dataTable">
            <thead>
            <tr>
                <th>#</th>
                <th>Book ID</th>
                <th>Title</th>
                <th>Student Name</th>
                <th>Student Number</th>
                <th>Borrowed Date</th>
                <th>Returned Date</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            @foreach($returnedBooks as $book)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $book->book->book_id }}</td>
                    <td>{{ $book->book->title }}</td>
                    <td>{{ $book->student->name }}</td>
                    <td>{{ $book->student->tcbt_student_number }}</td>
                    <td>{{ $book->borrowed_at }}</td>
                    <td>{{ $book->returned_at }}</td>
                    <td><span class="badge bg-success">Returned</span></td>
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
