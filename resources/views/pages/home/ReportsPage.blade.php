@extends('layouts.MainLayout')

@section('content')
    <div class="container">
        <h1>Reports</h1>

        <!-- Filter Form -->
        <form action="{{ route('reports') }}" method="GET" class="mb-4">
            <!-- TCBT Student Number -->
            <div class="form-group">
                <label for="tcbt_student_number">Student TCBT Number</label>
                <input
                    type="text"
                    class="form-control"
                    id="tcbt_student_number"
                    name="tcbt_student_number"
                    value="{{ request('tcbt_student_number') }}"
                    placeholder="Enter TCBT Number (e.g., TCBT1001)"
                >
            </div>

            <!-- Month Selection -->
            <div class="form-group">
                <label for="month">Select Month</label>
                <select name="month" id="month" class="form-control">
                    <option value="">-- Select Month --</option>
                    @php
                        $months = [
                            'January','February','March','April','May','June',
                            'July','August','September','October','November','December'
                        ];
                    @endphp
                    @foreach($months as $m)
                        <option value="{{ $m }}" {{ $m == request('month') ? 'selected' : '' }}>
                            {{ $m }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Year Selection -->
            <div class="form-group">
                <label for="year">Select Year</label>
                <select name="year" id="year" class="form-control">
                    <option value="">-- Select Year --</option>
                    @php
                        $years = ['2023','2024','2025','2026'];
                    @endphp
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ $y == request('year') ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Submit -->
            <button type="submit" class="btn btn-primary mt-4">Filter</button>
        </form>

        <!-- A) Individual Student Payments (If TCBT number is provided) -->
        @if($tcbtNumber)
            <h2>Payments for Student #{{ $tcbtNumber }}</h2>

            @if($studentPayments->count() > 0)
                <div class="table-responsive mb-5" id="dataTable">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Payment ID</th>
                            <th>Amount</th>
                            <th>Payment Date</th>
                            <th>Paid Month</th>
                            <th>Paid Year</th>
                            <th>Status</th>
                            <th>Created At</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($studentPayments as $payment)
                            <tr>
                                <td>{{ $payment->payment_id }}</td>
                                <td>{{ $payment->amount }}</td>
                                <td>{{ $payment->payment_date }}</td>
                                <td>{{ $payment->paid_month }}</td>
                                <td>{{ $payment->paid_year }}</td>
                                <td>{{ $payment->status }}</td>
                                <td>{{ $payment->created_at }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p>No payments found for this student.</p>
            @endif
        @endif

        <!-- B) All Students for Selected Month/Year with Paid/Unpaid -->
        @if($month && $year)
            <h2>Payment Status for {{ $month }} - {{ $year }}</h2>

            <!-- Show total sum of payment amounts in that month -->
            <h3>Total Revenue ({{ $month }} {{ $year }}): {{ $totalPayments }}</h3>

            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable">
                    <thead>
                    <tr>
                        <th>TCBT Number</th>
                        <th>Name</th>
                        <th>Grade</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($filteredStudents as $student)
                        <tr>
                            <td>{{ $student->tcbt_student_number }}</td>
                            <td>{{ $student->name }}</td>
                            <td>{{ $student->grade }}</td>
                            <td>{{ $student->payment_status }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">No students found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        @endif
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
