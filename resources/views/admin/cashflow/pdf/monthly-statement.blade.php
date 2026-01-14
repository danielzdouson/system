<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cashflow Statement - {{ $statement['period'] }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #667eea;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #333;
            margin: 0;
            font-size: 28px;
        }
        .header p {
            color: #666;
            margin: 10px 0 0 0;
            font-size: 16px;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }
        .summary-item {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            border-left: 4px solid #667eea;
        }
        .summary-item h4 {
            margin: 0 0 10px 0;
            color: #666;
            font-size: 14px;
            text-transform: uppercase;
        }
        .summary-item h3 {
            margin: 0;
            color: #333;
            font-size: 24px;
            font-weight: bold;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th {
            background: #667eea;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        .table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        .table tr:nth-child(even) {
            background: #f9f9f9;
        }
        .amount {
            text-align: right;
            font-weight: bold;
        }
        .amount.positive {
            color: #28a745;
        }
        .amount.negative {
            color: #dc3545;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Cashflow Statement</h1>
            <p>Period: {{ $statement['period'] }}</p>
            <p>Fiscal Year: {{ $statement['fiscal_year'] }}</p>
        </div>

        <!-- Summary -->
        <div class="section">
            <div class="summary-grid">
                <div class="summary-item">
                    <h4>Opening Balance</h4>
                    <h3>UGX {{ number_format($statement['summary']['opening_balance'], 0) }}</h3>
                </div>
                <div class="summary-item">
                    <h4>Net Cashflow</h4>
                    <h3 class="{{ $statement['summary']['net_cashflow'] >= 0 ? 'positive' : 'negative' }}">
                        UGX {{ number_format($statement['summary']['net_cashflow'], 0) }}
                    </h3>
                </div>
                <div class="summary-item">
                    <h4>Closing Balance</h4>
                    <h3>UGX {{ number_format($statement['summary']['closing_balance'], 0) }}</h3>
                </div>
            </div>
        </div>

        <!-- Operating Activities -->
        <div class="section">
            <div class="section-title">Operating Activities</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Subcategory</th>
                        <th>Inflows</th>
                        <th>Outflows</th>
                        <th>Net</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($statement['operating_activities']['details']))
                        @foreach($statement['operating_activities']['details'] as $detail)
                            <tr>
                                <td>{{ $detail['subcategory'] }}</td>
                                <td class="amount positive">UGX {{ number_format($detail['inflows'], 0) }}</td>
                                <td class="amount negative">UGX {{ number_format($detail['outflows'], 0) }}</td>
                                <td class="amount {{ $detail['net'] >= 0 ? 'positive' : 'negative' }}">
                                    UGX {{ number_format($detail['net'], 0) }}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" style="text-align: center; color: #666;">
                                No operating transactions found for this period.
                            </td>
                        </tr>
                    @endif
                    <tr style="font-weight: bold; background: #f0f0f0;">
                        <td>Total Operating Activities</td>
                        <td class="amount positive">UGX {{ number_format($statement['operating_activities']['inflows'], 0) }}</td>
                        <td class="amount negative">UGX {{ number_format($statement['operating_activities']['outflows'], 0) }}</td>
                        <td class="amount {{ $statement['operating_activities']['net'] >= 0 ? 'positive' : 'negative' }}">
                            UGX {{ number_format($statement['operating_activities']['net'], 0) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Investing Activities -->
        <div class="section">
            <div class="section-title">Investing Activities</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Subcategory</th>
                        <th>Inflows</th>
                        <th>Outflows</th>
                        <th>Net</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($statement['investing_activities']['details']))
                        @foreach($statement['investing_activities']['details'] as $detail)
                            <tr>
                                <td>{{ $detail['subcategory'] }}</td>
                                <td class="amount positive">UGX {{ number_format($detail['inflows'], 0) }}</td>
                                <td class="amount negative">UGX {{ number_format($detail['outflows'], 0) }}</td>
                                <td class="amount {{ $detail['net'] >= 0 ? 'positive' : 'negative' }}">
                                    UGX {{ number_format($detail['net'], 0) }}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" style="text-align: center; color: #666;">
                                No investing transactions found for this period.
                            </td>
                        </tr>
                    @endif
                    <tr style="font-weight: bold; background: #f0f0f0;">
                        <td>Total Investing Activities</td>
                        <td class="amount positive">UGX {{ number_format($statement['investing_activities']['inflows'], 0) }}</td>
                        <td class="amount negative">UGX {{ number_format($statement['investing_activities']['outflows'], 0) }}</td>
                        <td class="amount {{ $statement['investing_activities']['net'] >= 0 ? 'positive' : 'negative' }}">
                            UGX {{ number_format($statement['investing_activities']['net'], 0) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Financing Activities -->
        <div class="section">
            <div class="section-title">Financing Activities</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Subcategory</th>
                        <th>Inflows</th>
                        <th>Outflows</th>
                        <th>Net</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($statement['financing_activities']['details']))
                        @foreach($statement['financing_activities']['details'] as $detail)
                            <tr>
                                <td>{{ $detail['subcategory'] }}</td>
                                <td class="amount positive">UGX {{ number_format($detail['inflows'], 0) }}</td>
                                <td class="amount negative">UGX {{ number_format($detail['outflows'], 0) }}</td>
                                <td class="amount {{ $detail['net'] >= 0 ? 'positive' : 'negative' }}">
                                    UGX {{ number_format($detail['net'], 0) }}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" style="text-align: center; color: #666;">
                                No financing transactions found for this period.
                            </td>
                        </tr>
                    @endif
                    <tr style="font-weight: bold; background: #f0f0f0;">
                        <td>Total Financing Activities</td>
                        <td class="amount positive">UGX {{ number_format($statement['financing_activities']['inflows'], 0) }}</td>
                        <td class="amount negative">UGX {{ number_format($statement['financing_activities']['outflows'], 0) }}</td>
                        <td class="amount {{ $statement['financing_activities']['net'] >= 0 ? 'positive' : 'negative' }}">
                            UGX {{ number_format($statement['financing_activities']['net'], 0) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Generated on: {{ now()->format('F j, Y H:i:s') }}</p>
            <p>© {{ date('Y') }} SACCO Management System</p>
        </div>
    </div>
</body>
</html>
