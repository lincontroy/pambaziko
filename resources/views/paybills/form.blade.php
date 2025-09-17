@extends('layouts.app')

@section('title', isset($paybill) ? 'Edit Paybill' : 'Create Paybill')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ isset($paybill) ? 'Edit' : 'Create' }} Paybill</h3>
                </div>
                <form action="{{ isset($paybill) ? route('paybills.update', $paybill) : route('paybills.store') }}" method="POST">
                    @csrf
                    @if(isset($paybill))
                        @method('PUT')
                    @endif
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Paybill Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" 
                                value="{{ old('name', $paybill->name ?? '') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="paybill_number">Paybill Number</label>
                            <input type="text" class="form-control @error('paybill_number') is-invalid @enderror" id="paybill_number" 
                                name="paybill_number" value="{{ old('paybill_number', $paybill->paybill_number ?? '') }}" required>
                            @error('paybill_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="consumer_key">Consumer Key</label>
                            <input type="password" class="form-control @error('consumer_key') is-invalid @enderror" id="consumer_key" 
                                name="consumer_key" value="{{ old('consumer_key', $paybill->consumer_key ?? '') }}" required>
                            @error('consumer_key')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="consumer_secret">Consumer Secret</label>
                            <input type="password" class="form-control @error('consumer_secret') is-invalid @enderror" id="consumer_secret" 
                                name="consumer_secret" value="{{ old('consumer_secret', $paybill->consumer_secret ?? '') }}" required>
                            @error('consumer_secret')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="passkey">Passkey</label>
                            <input type="password" class="form-control @error('passkey') is-invalid @enderror" id="passkey" 
                                name="passkey" value="{{ old('passkey', $paybill->passkey ?? '') }}" required>
                            @error('passkey')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="daily_limit">Daily Limit</label>
                            <input type="number" class="form-control @error('daily_limit') is-invalid @enderror" id="daily_limit" 
                                name="daily_limit" value="{{ old('daily_limit', $paybill->daily_limit ?? 1000) }}" required>
                            @error('daily_limit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Save Paybill</button>
                        <a href="{{ route('paybills.index') }}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection