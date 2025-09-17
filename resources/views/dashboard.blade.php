@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalCampaigns }}</h3>
                    <p>Total Campaigns</p>
                </div>
                <div class="icon">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <a href="{{ route('campaigns.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $totalSent }}</h3>
                    <p>Total Sent</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <a href="{{ route('campaigns.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $totalFailed }}</h3>
                    <p>Total Failed</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <a href="{{ route('campaigns.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $quotaLeft }}</h3>
                    <p>Quota Left Today</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <a href="{{ route('paybills.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Campaigns</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Progress</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentCampaigns as $campaign)
                            <tr>
                                <td>{{ $campaign->name }}</td>
                                <td>
                                    <div class="progress progress-xs">
                                        <div class="progress-bar bg-{{ $campaign->status_color }}" style="width: {{ $campaign->progress_percentage }}%"></div>
                                    </div>
                                    <small>{{ $campaign->progress_percentage }}% Complete</small>
                                </td>
                                <td><span class="badge bg-{{ $campaign->status_color }}">{{ ucfirst($campaign->status) }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">System Status</h3>
                </div>
                <div class="card-body">
                    <div class="callout callout-info">
                        <h5>Queue Status</h5>
                        <p>Pending Jobs: {{ $pendingJobs }}</p>
                        <p>Failed Jobs: {{ $failedJobs }}</p>
                    </div>
                    <div class="callout callout-success">
                        <h5>Paybill Status</h5>
                        @foreach($paybills as $paybill)
                        <p>{{ $paybill->name }}: {{ $paybill->current_count }}/{{ $paybill->daily_limit }} used</p>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection