<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Expense Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #2d3748;
            margin-bottom: 5px;
        }

        .header p {
            color: #718096;
            margin-top: 0;
        }

        .user-info {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f7fafc;
            border-radius: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background-color: #4a5568;
            color: white;
            text-align: left;
            padding: 12px;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
        }

        tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            color: #718096;
            font-size: 0.9em;
        }

        .total {
            margin-top: 20px;
            text-align: right;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Expense Report</h1>
        <p>Generated on {{ date('F d, Y') }}</p>
    </div>

    <div class="user-info">
        <strong>User:</strong> {{ $user->name }}<br>
        <strong>Email:</strong> {{ $user->email }}<br>
        <strong>Period:</strong> All Time
    </div>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Group</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach ($expenses as $expense)
                @php $total += $expense->amount; @endphp
                <tr>
                    <td>{{ $expense->name }}</td>
                    <td>₹{{ number_format($expense->amount, 2) }}</td>
                    <td>{{ date('M d, Y', strtotime($expense->date)) }}</td>
                    <td>{{ $expense->group ? $expense->group->name : 'No Group' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        Total Expenses: ₹{{ number_format($total, 2) }}
    </div>

    <div class="footer">
        <p>This report was automatically generated from your Expense Tracker application.</p>
    </div>
</body>

</html>
