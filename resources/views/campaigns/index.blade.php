@extends('layouts.app')

@section('title', 'Campaigns')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">My Campaigns</h3>
                    <div class="card-tools">
                        <a href="{{ route('campaigns.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> New Campaign
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table id="campaignsTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Paybill</th>
                                <th>Total</th>
                                <th>Sent</th>
                                <th>Failed</th>
                                <th>Progress</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($campaigns as $campaign)
                            <tr>
                                <td>{{ $campaign->name }}</td>
                                <td>{{ $campaign->paybill->paybill_number }}</td>
                                <td>{{ $campaign->total_count }}</td>
                                <td>{{ $campaign->sent_count }}</td>
                                <td>{{ $campaign->failed_count }}</td>
                                <td>
                                    <div class="progress progress-xs">
                                        <div class="progress-bar bg-{{ $campaign->status_color }}" style="width: {{ $campaign->progress_percentage }}%"></div>
                                    </div>
                                    <small>{{ $campaign->progress_percentage }}% Complete</small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $campaign->status_color }}">
                                        {{ ucfirst($campaign->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('campaigns.show', $campaign) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('campaigns.details', $campaign) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-list"></i> Details
                                    </a>
                                    @if($campaign->status == 'pending' || $campaign->status == 'paused')
                                        <form action="{{ route('campaigns.start', $campaign) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="fas fa-play"></i> Start
                                            </button>
                                        </form>
                                    @endif
                                    @if($campaign->status == 'processing')
                                        <form action="{{ route('campaigns.pause', $campaign) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-sm">
                                                <i class="fas fa-pause"></i> Pause
                                            </button>
                                        </form>
                                    @endif
                                    @if($campaign->failed_count > 0)
                                        <form action="{{ route('campaigns.retry', $campaign) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            <button type="submit" class="btn btn-secondary btn-sm">
                                                <i class="fas fa-redo"></i> Retry Failed
                                            </button>
                                        </form>
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
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#campaignsTable').DataTable({
            "responsive": true,
            "autoWidth": false,
            "order": [[0, "desc"]]
        });
    });
</script>
@endsection