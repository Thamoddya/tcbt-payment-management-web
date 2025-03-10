@extends('layouts.MainLayout')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Books Management</h4>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addBookModal">
                            Add Book
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Book List</h5>
                        <div class="table-responsive">
                            <table id="dataTable" class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>ISBN</th>
                                    <th>Price</th>
                                    <th>Download QR</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($books as $book)
                                    <tr>
                                        <td>{{ $book->book_id }}</td>
                                        <td>{{ $book->title }}</td>
                                        <td>{{ $book->author }}</td>
                                        <td>{{ $book->isbn }}</td>
                                        <td>{{ $book->price}}</td>
                                        <td>
                                            <button class="btn btn-primary"
                                                    onclick="downloadQR('{{ $book->book_id }}')">
                                                Download QR
                                            </button>
                                            '
                                        </td>
                                        <td>
                                            <button class="btn btn-warning" data-bs-toggle="modal"
                                                    data-bs-target="#editBookModal{{$book->id}}">Edit
                                            </button>
                                            <form action="{{ route('books.destroy', $book->id) }}" method="POST"
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Delete</button>
                                            </form>
                                            @hasrole('Super_Admin')
                                            {{--If book is NotAvailable show available button--}}
                                            @if($book->status == 'Not Available')
                                                <form action="{{ route('books.makeAvailable', $book->id) }}"
                                                      method="POST"
                                                      class="d-inline">
                                                    @csrf
                                                    @method('POST')
                                                    <input type="hidden" name="status" value="Available">
                                                    <button type="submit" class="btn btn-success">Force Available
                                                    </button>
                                                </form>
                                            @endif
                                            @endhasrole
                                        </td>
                                    </tr>

                                    <!-- Edit Book Modal -->
                                    <div class="modal fade" id="editBookModal{{$book->id}}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('books.update', $book->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Book</h5>
                                                        <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <label>Title</label>
                                                        <input type="text" class="form-control" name="title"
                                                               value="{{ $book->title }}" required>
                                                        <label>Author</label>
                                                        <input type="text" class="form-control" name="author"
                                                               value="{{ $book->author }}" required>
                                                        <label>ISBN</label>
                                                        <input type="text" class="form-control" name="price"
                                                               value="{{ $book->price }}" required>
                                                        <input type="text" class="form-control" name="isbn"
                                                               value="{{ $book->isbn }}" required>
                                                        <label>Price</label>

                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-primary">Update</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Book Modal -->
        <div class="modal fade" id="addBookModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('books.store') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Add Book</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <label>Title</label>
                            <input type="text" class="form-control" name="title" required>
                            <label>Author</label>
                            <input type="text" class="form-control" name="author" required>
                            <label>ISBN</label>
                            <input type="text" class="form-control" name="isbn" required>
                            <label>Price</label>
                            <input type="text" class="form-control" name="price" required>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <script>
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

        function downloadQR(bookID) {
            // Create a hidden div to generate the QR
            const qrContainer = document.createElement("div");
            const qr = new QRCode(qrContainer, {
                text: `${bookID}`,
                width: 400,
                height: 400,
                correctLevel: QRCode.CorrectLevel.H
            });

            // Wait for QR to render
            setTimeout(() => {
                const canvas = qrContainer.querySelector("canvas");
                if (canvas) {
                    // Create a link to download
                    const link = document.createElement("a");
                    link.href = canvas.toDataURL("image/png");
                    link.download = `${bookID}-qr.png`;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                } else {
                    console.error("QR Code generation failed.");
                }
            }, 500);
        }
    </script>
@endsection
