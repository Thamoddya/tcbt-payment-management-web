@extends('layouts.MainLayout')

@section('content')
    <div class="container-fluid">
        {{-- Student Count --}}

        @hasrole('Super_Admin')
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Students</h4>
                            <div class="row text-center">

                                <!-- Total and Active Students -->
                                <div class="col-sm-6 col-md-3 mb-3">
                                    <div class="card shadow-sm">
                                        <div class="card-body">
                                            <h5 class="mb-1">Total Students</h5>
                                            <p class="mb-0 fs-4 fw-bold">
                                                {{ $totalStudents }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-md-3 mb-3">
                                    <div class="card shadow-sm">
                                        <div class="card-body">
                                            <h5 class="mb-1">Active Students</h5>
                                            <p class="mb-0 fs-4 fw-bold">
                                                {{ $totalActiveStudents }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Inactive Students and Pending Payments -->
                                <div class="col-sm-6 col-md-3 mb-3">
                                    <div class="card shadow-sm">
                                        <div class="card-body">
                                            <h5 class="mb-1">Inactive Students</h5>
                                            <p class="mb-0 fs-4 fw-bold">
                                                {{ $totalInactiveStudents }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-md-3 mb-3">
                                    <div class="card shadow-sm">
                                        <div class="card-body">
                                            <h5 class="mb-1">Pending Payments</h5>
                                            <p class="mb-0 fs-4 fw-bold">
                                                {{ $pendingPayments }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <h4>Monthly Payments</h4>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Total Payment (Amount)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($months as $index => $month)
                                <tr>
                                    <td>{{ $month }}</td>
                                    <td>Rs.{{ $chartData[$index] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8 d-flex align-items-strech">
                    <div class="card w-100">
                        <div class="card-body">
                            <div class="d-sm-flex d-block align-items-center justify-content-between mb-9">
                                <div class="mb-3 mb-sm-0">
                                    <h5 class="card-title fw-semibold">Payment Overview</h5>
                                </div>
                                <div>
                                    <select class="form-select">
                                        <option value="1">Payment Data</option>
                                    </select>
                                </div>
                            </div>
                            <div id="chart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="row">
                        <div class="col-lg-12">
                            <!-- Yearly Breakup -->
                            <div class="card overflow-hidden">
                                <div class="card-body p-4">
                                    <h5 class="card-title mb-9 fw-semibold">Yearly Earnings</h5>
                                    <div class="row align-items-center">
                                        <div class="col-8">
                                            <h4 class="fw-semibold mb-3">
                                                Rs.{{ $yearEarning }}
                                            </h4>

                                            <div class="d-flex align-items-center mb-3">
                                                <span
                                                    class="me-1 rounded-circle bg-light-success round-20 d-flex align-items-center justify-content-center">
                                                    <i class="ti ti-arrow-up-left text-success"></i>
                                                </span>
                                                <p class="text-dark me-1 fs-3 mb-0">+9%</p>
                                                <p class="fs-3 mb-0">last year</p>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <div class="me-4">
                                                    <span class="round-8 bg-primary rounded-circle me-2 d-inline-block"></span>
                                                    <span class="fs-2">
                                                        {{ Carbon\Carbon::now()->format('Y') }}
                                                    </span>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="d-flex justify-content-center">
                                                <div id="breakup"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <!-- Monthly Earnings -->
                            <div class="card">
                                <div class="card-body">
                                    <div class="row alig n-items-start">
                                        <div class="col-8">
                                            <h5 class="card-title mb-9 fw-semibold"> Monthly Earnings </h5>
                                            <h4 class="fw-semibold mb-3">RS.{{ $thisMonthEarning }}</h4>
                                            <div class="d-flex align-items-center pb-1">
                                                <span
                                                    class="me-2 rounded-circle bg-light-danger round-20 d-flex align-items-center justify-content-center">
                                                    <i class="ti ti-arrow-down-right text-danger"></i>
                                                </span>
                                                <p class="text-dark me-1 fs-3 mb-0">+9%</p>
                                                <p class="fs-3 mb-0">last year</p>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="d-flex justify-content-end">
                                                <div
                                                    class="text-white bg-secondary rounded-circle p-6 d-flex align-items-center justify-content-center">
                                                    <i class="ti ti-currency-dollar fs-6"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="earning"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endhasrole
        <div class="row">
            <div class="col-lg-12 d-flex align-items-stretch">
                <div class="card w-100">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-semibold mb-4">Recent Payments</h5>
                        <div class="table-responsive">
                            <table class="table text-nowrap mb-0 align-middle">
                                <thead class="text-dark fs-4">
                                    <tr>
                                        <th class="border-bottom-0">
                                            <h6 class="fw-semibold mb-0">Id</h6>
                                        </th>
                                        <th class="border-bottom-0">
                                            <h6 class="fw-semibold mb-0">Student Name</h6>
                                        </th>
                                        <th class="border-bottom-0">
                                            <h6 class="fw-semibold mb-0">Month</h6>
                                        </th>
                                        <th class="border-bottom-0">
                                            <h6 class="fw-semibold mb-0">Amount</h6>
                                        </th>
                                        <th class="border-bottom-0">
                                            <h6 class="fw-semibold mb-0">Status</h6>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($lastFivePayments as $index => $payment)
                                        <tr>
                                            <td class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0">
                                                    {{ $index + 1 }}
                                                </h6>
                                            </td>
                                            <td class="border-bottom-0">
                                                <h6 class="fw-semibold mb-1">
                                                    {{ $payment->student->name }}
                                                </h6>
                                                <span class="fw-normal">
                                                    Grade : {{ $payment->student->grade }}
                                                </span>
                                            </td>
                                            <td class="border-bottom-0">
                                                <p class="mb-0 fw-normal">
                                                    {{ $payment->paid_month }}
                                                </p>
                                            </td>
                                            <td class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0 fs-4">
                                                    Rs.{{ number_format($payment->amount, 2) }}
                                                </h6>
                                            </td>
                                            <td class="border-bottom-0">
                                                @if (strtolower($payment->status) == 'completed')
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span
                                                            class="badge bg-primary rounded-3 fw-semibold">Completed</span>
                                                    </div>
                                                @elseif (strtolower($payment->status) == 'pending')
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="badge bg-warning rounded-3 fw-semibold">Pending</span>
                                                    </div>
                                                @else
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="badge bg-danger rounded-3 fw-semibold">Failed</span>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
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
        document.addEventListener('DOMContentLoaded', function() {
            var chartData = Object.values(@json($chartData));
            var months = Object.values(@json($months));

            console.log('Chart Data:', chartData);
            console.log('Months:', months);

            var chart = {
                series: [{
                    name: "Earnings this month:",
                    data: chartData
                }],
                chart: {
                    type: "bar",
                    height: 345,
                    offsetX: -15,
                    toolbar: {
                        show: true
                    },
                    foreColor: "#adb0bb",
                    fontFamily: 'inherit',
                    sparkline: {
                        enabled: false
                    },
                },
                colors: ["#5D87FF", "#49BEFF"],
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: "35%",
                        borderRadius: [6],
                        borderRadiusApplication: 'end',
                        borderRadiusWhenStacked: 'all'
                    },
                },
                xaxis: {
                    type: "category",
                    categories: months, // Ensure 'months' is an array
                    labels: {
                        style: {
                            cssClass: "grey--text lighten-2--text fill-color"
                        },
                    },
                },
                yaxis: {
                    show: true,
                    min: 0,
                    tickAmount: 4,
                    labels: {
                        style: {
                            cssClass: "grey--text lighten-2--text fill-color"
                        },
                    },
                },
                stroke: {
                    show: true,
                    width: 3,
                    lineCap: "butt",
                    colors: ["transparent"],
                },
                tooltip: {
                    theme: "light"
                },
                responsive: [{
                    breakpoint: 600,
                    options: {
                        plotOptions: {
                            bar: {
                                borderRadius: 3,
                            }
                        },
                    }
                }]
            };

            var chartInstance = new ApexCharts(document.querySelector("#chart"), chart);
            chartInstance.render();


            var donutData = @json($donutData);

            var breakup = {
                color: "#adb5bd",
                series: donutData,
                labels: ["{{ Carbon\Carbon::now()->year }}", "{{ Carbon\Carbon::now()->subYear()->year }}",
                    "Previous Years"
                ],
                chart: {
                    width: 180,
                    type: "donut",
                    fontFamily: "Plus Jakarta Sans', sans-serif",
                    foreColor: "#adb0bb",

                },
                plotOptions: {
                    pie: {
                        startAngle: 0,
                        endAngle: 360,
                        donut: {
                            size: '75%',
                        },
                    },
                },
                stroke: {
                    show: false,
                },
                dataLabels: {
                    enabled: false,
                },
                legend: {
                    show: false,
                },
                colors: ["#5D87FF", "#ecf2ff", "#F9F9FD"],
                responsive: [{
                    breakpoint: 991,
                    options: {
                        chart: {
                            width: 150,
                        },
                    },
                }],
                tooltip: {
                    theme: "dark",
                    fillSeriesColor: true,
                },
            };

            var chart = new ApexCharts(document.querySelector("#breakup"), breakup);
            chart.render();


            // =====================================
            // Earning
            // =====================================
            var earning = {
                chart: {
                    id: "sparkline3",
                    type: "area",
                    height: 60,
                    sparkline: {
                        enabled: true,
                    },
                    group: "sparklines",
                    fontFamily: "Plus Jakarta Sans', sans-serif",
                    foreColor: "#adb0bb",
                },
                series: [{
                    name: "Earnings",
                    color: "#49BEFF",
                    data: [25, 66, 20, 40, 12, 58, 20],
                }, ],
                stroke: {
                    curve: "smooth",
                    width: 2,
                },
                fill: {
                    colors: ["#f3feff"],
                    type: "solid",
                    opacity: 0.05,
                },

                markers: {
                    size: 0,
                },
                tooltip: {
                    theme: "dark",
                    fixed: {
                        enabled: true,
                        position: "right",
                    },
                    x: {
                        show: false,
                    },
                },
            };
            new ApexCharts(document.querySelector("#earning"), earning).render();
        });
    </script>
@endsection
