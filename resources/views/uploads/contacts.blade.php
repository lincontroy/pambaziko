@extends('layouts.app')

@section('title', 'Upload Contacts')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Contacts: {{ $upload->filename }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('uploads.show', $upload) }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Upload
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table id="contactsTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Phone</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Attempts</th>
                                <th>Last Attempt</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($contacts as $contact)
                            <tr>
                                <td>{{ $contact->phone }}</td>
                                <td>{{ number_format($contact->amount, 2) }}</td>
                                <td>
                                    <span class="badge badge-{{ $contact->status_color }}">
                                        {{ ucfirst($contact->status) }}
                                    </span>
                                </td>
                                <td>{{ $contact->attempts }}</td>
                                <td>{{ $contact->last_attempt_at ? $contact->last_attempt_at->format('Y-m-d H:i') : 'Never' }}</td>
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
        $('#contactsTable').DataTable({
            "responsive": true,
            "autoWidth": false,
            "pageLength": 25
        });
    });
</script>
@endsection