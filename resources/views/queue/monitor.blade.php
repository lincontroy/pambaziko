@extends('layouts.app')

@section('title', 'Queue Monitor')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Queue Monitor</h3>
                    <div class="card-tools">
                        <form action="{{ route('queue.process', config('app.queue_secret')) }}" method="GET" style="display:inline-block;">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-play"></i> Process Queue
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="queueTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="pending-tab" data-toggle="tab" href="#pending" role="tab" aria-controls="pending" aria-selected="true">
                                Pending Jobs <span class="badge badge-primary">{{ $pendingJobs->count() }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="failed-tab" data-toggle="tab" href="#failed" role="tab" aria-controls="failed" aria-selected="false">
                                Failed Jobs <span class="badge badge-danger">{{ $failedJobs->count() }}</span>
                            </a>
                        </li>
                    </ul>
                    
                    <div class="tab-content" id="queueTabsContent">
                        <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Queue</th>
                                            <th>Attempts</th>
                                            <th>Created At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pendingJobs as $job)
                                        <tr>
                                            <td>{{ $job->id }}</td>
                                            <td>{{ $job->queue }}</td>
                                            <td>{{ $job->attempts }}</td>
                                            <td>{{ $job->created_at}}</td>
                                            <td>
                                                <form action="{{ route('queue.delete', $job->id) }}" method="POST" style="display:inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No pending jobs</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="failed" role="tabpanel" aria-labelledby="failed-tab">
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Queue</th>
                                            <th>Exception</th>
                                            <th>Failed At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($failedJobs as $job)
                                        <tr>
                                            <td>{{ $job->id }}</td>
                                            <td>{{ $job->queue }}</td>
                                            <td>{{ Str::limit($job->exception, 100) }}</td>
                                            <td>{{ $job->failed_at }}</td>
                                            <td>
                                                <form action="{{ route('queue.retry', $job->id) }}" method="POST" style="display:inline-block;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="fas fa-redo"></i> Retry
                                                    </button>
                                                </form>
                                                <form action="{{ route('queue.delete', $job->id) }}" method="POST" style="display:inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No failed jobs</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection