@extends('layouts.app')

@section('title', 'Campaign Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Campaign Details: {{ $campaign->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('campaigns.show', $campaign) }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Campaign
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="btn-group">
                                <button type="button" class="btn btn-default filter-btn" data-status="all">
                                    All <span class="badge badge-light">{{ $campaign->total_count }}</span>
                                </button>
                                <button type="button" class="btn btn-success filter-btn" data-status="sent">
                                    Sent <span class="badge badge-light">{{ $campaign->sent_count }}</span>
                                </button>
                                <button type="button" class="btn btn-danger filter-btn" data-status="failed">
                                    Failed <span class="badge badge-light">{{ $campaign->failed_count }}</span>
                                </button>
                                <button type="button" class="btn btn-secondary filter-btn" data-status="pending">
                                    Pending <span class="badge badge-light">{{ $campaign->total_count - $campaign->sent_count - $campaign->failed_count }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <table id="campaignDetailsTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Phone</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Attempts</th>
                                <th>Last Attempt</th>
                                <th>Response</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($contacts as $contact)
                            <tr data-status="{{ $contact->status }}">
                                <td>{{ $contact->phone }}</td>
                                <td>{{ number_format($contact->amount, 2) }}</td>
                                <td>
                                    <span class="badge badge-{{ $contact->status_color }}">
                                        {{ ucfirst($contact->status) }}
                                    </span>
                                </td>
                                <td>{{ $contact->attempts }}</td>
                                <td>{{ $contact->last_attempt_at ? $contact->last_attempt_at->format('Y-m-d H:i') : 'Never' }}</td>
                                <td>
                                    @if($contact->response_json)
                                        <button class="btn btn-sm btn-info view-response" data-response="{{ json_encode($contact->response_json, JSON_PRETTY_PRINT) }}">
                                            <i class="fas fa-eye"></i> View
                                        </button>
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

<!-- Response Modal -->
<div class="modal fade" id="responseModal" tabindex="-1" role="dialog" aria-labelledby="responseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="responseModalLabel">STK Push Response</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <pre id="responseContent" class="bg-light p-3"></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        var table = $('#campaignDetailsTable').DataTable({
            "responsive": true,
            "autoWidth": false,
            "pageLength": 25
        });
        
        // Filter by status
        $('.filter-btn').on('click', function() {
            var status = $(this).data('status');
            if (status === 'all') {
                table.columns().search('').draw();
            } else {
                table.columns(2).search(status).draw();
            }
        });
        
        // View response
        $('.view-response').on('click', function() {
            var response = $(this).data('response');
            $('#responseContent').text(response);
            $('#responseModal').modal('show');
        });
    });
</script>
@endsection