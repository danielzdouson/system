<!-- Investment Dashboard Widget -->
<div class="col-md-3">
    <div class="card bg-gradient-primary text-white">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0">{{ number_format($investmentStats['total_invested'], 2) }}</h4>
                    <p class="mb-0">Total Invested</p>
                </div>
                <div class="text-right">
                    <i class="fas fa-chart-line fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <span class="text-white">ROI: {{ number_format($investmentStats['total_roi'], 2) }}%</span>
            <a href="{{ route('admin.investments.index') }}" class="text-white">
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

<div class="col-md-3">
    <div class="card bg-gradient-success text-white">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0">{{ number_format($investmentStats['total_returns'], 2) }}</h4>
                    <p class="mb-0">Total Returns</p>
                </div>
                <div class="text-right">
                    <i class="fas fa-coins fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <span class="text-white">Active: {{ $investmentStats['active_count'] }}</span>
            <a href="{{ route('admin.investments.index') }}" class="text-white">
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

<div class="col-md-3">
    <div class="card bg-gradient-info text-white">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0">{{ number_format($investmentStats['total_current_value'], 2) }}</h4>
                    <p class="mb-0">Current Value</p>
                </div>
                <div class="text-right">
                    <i class="fas fa-wallet fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <span class="text-white">Matured: {{ $investmentStats['matured_count'] }}</span>
            <a href="{{ route('admin.investments.index') }}" class="text-white">
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

<div class="col-md-3">
    <div class="card bg-gradient-warning text-white">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0">{{ $investmentStats['maturing_soon'] }}</h4>
                    <p class="mb-0">Maturing Soon (30 days)</p>
                </div>
                <div class="text-right">
                    <i class="fas fa-clock fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <span class="text-white">View Details</span>
            <a href="{{ route('admin.investments.index', ['status' => 'ACTIVE']) }}" class="text-white">
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>
