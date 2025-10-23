<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .card {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            border: 1px solid #e3e6f0;
        }
        .summary-card {
            border-left: 4px solid #4e73df;
        }
        .positive-amount {
            color: #1cc88a;
            font-weight: bold;
        }
        .negative-amount {
            color: #e74a3b;
            font-weight: bold;
        }
        .table-hover tbody tr:hover {
            background-color: #f8f9fc;
        }
        .customer-name {
            font-weight: 600;
            color: #2d3748;
        }
        .customer-telephone {
            font-size: 0.875rem;
            color: #718096;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-file-invoice-dollar text-primary"></i>
                Loan Report
            </h1>
            <div>
                <button onclick="refreshReport()" class="btn btn-primary">
                    <i class="fas fa-sync-alt"></i> Refresh Report
                </button>
                <button onclick="printReport()" class="btn btn-success">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2 summary-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Customers
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ count($report) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2 summary-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Amount Difference
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    Rs. {{ number_format(collect($report)->sum('amount_difference'), 2) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2 summary-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Avg Days Not Settled
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format(collect($report)->avg('days_not_settled'), 1) }} days
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2 summary-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Today's Total Amount
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    Rs. {{ number_format(collect($report)->sum('today_amount'), 2) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-coins fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-table me-2"></i>Loan Details Report
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive" id="reportTable">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead class="table-dark">
                            <tr>
                                <th>Customer Short Name</th>
                                <th>Customer Name</th>
                                <th>Telephone No</th>
                                <th>Amount Difference</th>
                                <th>Last Loan Taken</th>
                                <th>Last Loan Settled</th>
                                <th>Days Not Settled</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($report as $row)
                            <tr>
                                <td class="fw-bold">{{ $row['customer_short_name'] }}</td>
                                <td>
                                    <div class="customer-name">{{ $row['customer_name'] }}</div>
                                </td>
                                <td>
                                    <div class="customer-telephone">
                                        <i class="fas fa-phone me-1"></i>
                                        {{ $row['customer_telephone'] }}
                                    </div>
                                </td>
                                <td class="{{ $row['amount_difference'] >= 0 ? 'positive-amount' : 'negative-amount' }}">
                                    Rs. {{ number_format($row['amount_difference'], 2) }}
                                </td>
                                <td>
                                    @if($row['last_loan_taken'])
                                        {{ \Carbon\Carbon::parse($row['last_loan_taken'])->format('M d, Y') }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($row['last_loan_settled'])
                                        {{ \Carbon\Carbon::parse($row['last_loan_settled'])->format('M d, Y') }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $row['days_not_settled'] > 30 ? 'bg-danger' : ($row['days_not_settled'] > 15 ? 'bg-warning' : 'bg-success') }}">
                                        {{ $row['days_not_settled'] }} days
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-exclamation-circle fa-2x mb-3"></i>
                                    <br>
                                    No data available for the report.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if(count($report) > 0)
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3" class="text-end">Total:</th>
                                <th class="{{ collect($report)->sum('amount_difference') >= 0 ? 'positive-amount' : 'negative-amount' }}">
                                    Rs. {{ number_format(collect($report)->sum('amount_difference'), 2) }}
                                </th>
                                <th colspan="2"></th>
                                <th>
                                    {{ number_format(collect($report)->avg('days_not_settled'), 1) }} days avg
                                </th>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Last Updated -->
        <div class="text-center text-muted small">
            <i class="fas fa-clock me-1"></i>
            Last updated: {{ now()->format('M d, Y h:i A') }}
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function refreshReport() {
            const btn = event.target;
            const originalText = btn.innerHTML;
            
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
            btn.disabled = true;
            
            // Simple page reload for demonstration
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }
        
        function printReport() {
            const printContent = document.getElementById('reportTable').innerHTML;
            const originalContent = document.body.innerHTML;
            
            document.body.innerHTML = `
                <html>
                    <head>
                        <title>Loan Report - {{ now()->format('M d, Y') }}</title>
                        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
                    </head>
                    <body>
                        <div class="container mt-4">
                            <h2 class="text-center mb-4">Loan Report</h2>
                            ${printContent}
                            <div class="text-center mt-4 text-muted">
                                Generated on: {{ now()->format('M d, Y h:i A') }}
                            </div>
                        </div>
                    </body>
                </html>
            `;
            
            window.print();
            document.body.innerHTML = originalContent;
            window.location.reload();
        }
        
        // Auto-refresh every 5 minutes (optional)
        setInterval(() => {
            console.log('Auto-refreshing report...');
            window.location.reload();
        }, 300000); // 5 minutes
    </script>
</body>
</html>