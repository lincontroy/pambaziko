@extends('layouts.app')

@section('title', 'Campaign Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Campaign: {{ $campaign->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('campaigns.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Campaigns
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Name:</th>
                                    <td>{{ $campaign->name }}</td>
                                </tr>
                                <tr>
                                    <th>Paybill:</th>
                                    <td>{{ $campaign->paybill->name }} ({{ $campaign->paybill->paybill_number }})</td>
                                </tr>
                                <tr>
                                    <th>Upload:</th>
                                    <td>{{ $campaign->upload->filename }}</td>
                                </tr>
                                <tr>
                                    <th>Total Contacts:</th>
                                    <td>{{ $campaign->total_count }}</td>
                                </tr>
                                <tr>
                                    <th>Sent:</th>
                                    <td>{{ $campaign->sent_count }}</td>
                                </tr>
                                <tr>
                                    <th>Failed:</th>
                                    <td>{{ $campaign->failed_count }}</td>
                                </tr>
                                <tr>
                                    <th>Progress:</th>
                                    <td>
                                        <div class="progress progress-xs">
                                            <div class="progress-bar bg-{{ $campaign->status_color }}" style="width: {{ $campaign->progress_percentage }}%"></div>
                                        </div>
                                        {{ $campaign->progress_percentage }}% Complete
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="badge bg-{{ $campaign->status_color }}">
                                            {{ ucfirst($campaign->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Started At:</th>
                                    <td>{{ $campaign->started_at ? $campaign->started_at->format('Y-m-d H:i') : 'Not started' }}</td>
                                </tr>
                                <tr>
                                    <th>Completed At:</th>
                                    <td>{{ $campaign->completed_at ? $campaign->completed_at->format('Y-m-d H:i') : 'Not completed' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h4>Campaign Actions</h4>
                        <div class="btn-group">
                            @if($campaign->status == 'pending' || $campaign->status == 'paused')
                                <form action="{{ route('campaigns.start', $campaign) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-play"></i> Start Campaign
                                    </button>
                                </form>
                            @endif
                            @if($campaign->status == 'processing')
                                <form action="{{ route('campaigns.pause', $campaign) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-pause"></i> Pause Campaign
                                    </button>
                                </form>
                            @endif
                            @if($campaign->failed_count > 0)
                                <form action="{{ route('campaigns.retry', $campaign) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary">
                                        <i class="fas fa-redo"></i> Retry Failed
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection