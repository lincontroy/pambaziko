@extends('layouts.app')

@section('title', 'Paybills')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">My Paybills</h3>
                    <div class="card-tools">
                        <a href="{{ route('paybills.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add New Paybill
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table id="paybillsTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Paybill Number</th>
                                <th>Daily Limit</th>
                                <th>Used Today</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($paybills as $paybill)
                            <tr>
                                <td>{{ $paybill->name }}</td>
                                <td>{{ $paybill->paybill_number }}</td>
                                <td>{{ $paybill->daily_limit }}</td>
                                <td>{{ $paybill->current_count }}</td>
                                <td>
                                    <span class="badge badge-{{ $paybill->hasQuota() ? 'success' : 'danger' }}">
                                        {{ $paybill->hasQuota() ? 'Active' : 'Limit Reached' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('paybills.edit', $paybill) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('paybills.destroy', $paybill) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
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
        $('#paybillsTable').DataTable({
            "responsive": true,
            "autoWidth": false,
        });
    });
</script>
@endsection