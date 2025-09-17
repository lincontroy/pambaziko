@extends('layouts.app')

@section('title', 'Uploads')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">My Uploads</h3>
                    <div class="card-tools">
                        <a href="{{ route('uploads.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> New Upload
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table id="uploadsTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Filename</th>
                                <th>Rows</th>
                                <th>Status</th>
                                <th>Uploaded At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($uploads as $upload)
                            <tr>
                                <td>{{ $upload->filename }}</td>
                                <td>{{ $upload->rows_count }}</td>
                                <td>
                                    <span class="badge badge-{{ $upload->status_color }}">
                                        {{ ucfirst($upload->status) }}
                                    </span>
                                </td>
                                <td>{{ $upload->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('uploads.show', $upload) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('uploads.contacts', $upload) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-users"></i> Contacts
                                    </a>
                                    <form action="{{ route('uploads.destroy', $upload) }}" method="POST" style="display:inline-block;">
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
        $('#uploadsTable').DataTable({
            "responsive": true,
            "autoWidth": false,
            "order": [[3, "desc"]]
        });
    });
</script>
@endsection