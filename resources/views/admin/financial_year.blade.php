<!DOCTYPE html>
<html>
<head>
    <title>Financial Year Records</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #aaa;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background: #f4f4f4;
        }
        h2 {
            text-align: center;
        }
    </style>
</head>
<body>
    <h2>Financial Year Summary</h2>
    <table>
        <thead>
            <tr>
                <th>ID Number</th>
                <th>Name</th>
                <th>Contact</th>
                <th>Savings</th>
                <th>Welfare Funds</th>
                <th>Education Funds</th>
                <th>Fines Outstanding</th>
                <th>Fines Collected</th>
                <th>Loan Balance</th>
                <th>Shares</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
            <tr>
                <td>{{ $record->id_number }}</td>
                <td>{{ $record->name }}</td>
                <td>{{ $record->contact }}</td>
                <td>{{ $record->savings }}</td>
                <td>{{ $record->welfare_funds }}</td>
                <td>{{ $record->education_funds }}</td>
                <td>{{ $record->fines_outstanding }}</td>
                <td>{{ $record->fines_collected }}</td>
                <td>{{ $record->loan_balance }}</td>
                <td>{{ $record->shares }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
