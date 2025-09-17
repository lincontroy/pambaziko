@extends('layouts.app')

@section('title', 'Upload Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Upload Details: {{ $upload->filename }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('uploads.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Uploads
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Filename:</th>
                                    <td>{{ $upload->filename }}</td>
                                </tr>
                                <tr>
                                    <th>Rows:</th>
                                    <td>{{ $upload->rows_count }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="badge badge-{{ $upload->status_color }}">
                                            {{ ucfirst($upload->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Uploaded At:</th>
                                    <td>{{ $upload->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h4>Create Campaign from this Upload</h4>
                        <form action="{{ route('campaigns.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="upload_id" value="{{ $upload->id }}">
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="name">Campaign Name</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                        value="{{ old('name', 'Campaign from ' . $upload->filename) }}" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="paybill_id">Paybill</label>
                                    <select class="form-control" id="paybill_id" name="paybill_id" required>
                                        <option value="">Select Paybill</option>
                                        @foreach($paybills as $paybill)
                                            <option value="{{ $paybill->id }}" {{ old('paybill_id') == $paybill->id ? 'selected' : '' }}>
                                                {{ $paybill->name }} ({{ $paybill->paybill_number }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-success">Create Campaign</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection