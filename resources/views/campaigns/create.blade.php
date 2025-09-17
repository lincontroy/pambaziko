@extends('layouts.app')

@section('title', 'Create Campaign')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create New Campaign</h3>
                </div>
                <form action="{{ route('campaigns.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Campaign Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" 
                                value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="upload_id">Select Upload</label>
                            <select class="form-control @error('upload_id') is-invalid @enderror" id="upload_id" name="upload_id" required>
                                <option value="">Select Upload</option>
                                @foreach($uploads as $upload)
                                    <option value="{{ $upload->id }}" {{ old('upload_id') == $upload->id ? 'selected' : '' }}>
                                        {{ $upload->filename }} ({{ $upload->rows_count }} contacts)
                                    </option>
                                @endforeach
                            </select>
                            @error('upload_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="paybill_id">Select Paybill</label>
                            <select class="form-control @error('paybill_id') is-invalid @enderror" id="paybill_id" name="paybill_id" required>
                                <option value="">Select Paybill</option>
                                @foreach($paybills as $paybill)
                                    <option value="{{ $paybill->id }}" {{ old('paybill_id') == $paybill->id ? 'selected' : '' }}>
                                        {{ $paybill->name }} ({{ $paybill->paybill_number }})
                                    </option>
                                @endforeach
                            </select>
                            @error('paybill_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Create Campaign</button>
                        <a href="{{ route('campaigns.index') }}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection