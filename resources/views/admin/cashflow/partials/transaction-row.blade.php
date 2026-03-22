<tr>
    <td>
        <span class="date-badge">
            {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y') }}
        </span>
    </td>
    <td>
        <span class="type-badge {{ $transaction->type == 'income' || $transaction->type == 'INFLOW' ? 'inflow' : 'outflow' }}">
            {{ strtoupper($transaction->type) }}
        </span>
    </td>
    <td>
        <span class="category-badge {{ strtolower($transaction->category) }}">
            {{ $transaction->category }}
        </span>
    </td>
    <td>
        @if($transaction->is_reallocation)
            <span class="badge bg-secondary">
                <i class="fas fa-exchange-alt me-1"></i> Reallocation
            </span>
        @else
            <span class="badge bg-primary">
                <i class="fas fa-globe me-1"></i> External
            </span>
        @endif
    </td>
    <td>
        <div>
            <strong>{{ $transaction->description }}</strong>
            @if($transaction->reference_number)
                <br><small class="text-muted">Ref: {{ $transaction->reference_number }}</small>
            @endif
            @if(isset($transaction->member_id) && $transaction->member_id)
                <br><small class="text-info">Member ID: {{ $transaction->member_id }}</small>
            @endif
        </div>
    </td>
    <td>
        <span class="amount-display {{ $transaction->type == 'income' || $transaction->type == 'INFLOW' ? 'inflow' : 'outflow' }}">
            UGX {{ number_format($transaction->amount, 0) }}
        </span>
    </td>
    <td>{{ $transaction->payment_method }}</td>
    <td>
        <span class="status-badge {{ strtolower($transaction->status) }}">
            {{ ucfirst($transaction->status) }}
        </span>
    </td>
    <td>
        <span class="badge bg-info">
            {{ $transaction->transaction_source ?? 'Manual Entry' }}
        </span>
    </td>
    <td>
        <div class="dropdown">
            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-ellipsis-h"></i>
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a href="#" class="dropdown-item" onclick="showTransactionDetails('{{ $transaction->source_model }}', {{ $transaction->id }})">
                        <i class="fas fa-eye"></i>
                        View Details
                    </a>
                </li>
                @if($transaction->status == 'PENDING' && auth()->user()->can('approve-cashflow'))
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a href="#" class="dropdown-item" onclick="approveTransaction('{{ $transaction->source_model }}', {{ $transaction->id }})">
                            <i class="fas fa-check-circle"></i>
                            Approve Transaction
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </td>
</tr>
