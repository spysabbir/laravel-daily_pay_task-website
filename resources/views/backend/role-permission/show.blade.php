@extends('layouts.template_master')

@section('title', 'Assigning Role Permission - Show')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Assigning Role Permission - Show</h3>
                <a href="{{ route('backend.role-permission.index') }}" class="btn btn-info">Back to List</a>
            </div>
            <div class="card-body">
                <div class="mb-3 text-center border p-3">
                    <h4>Role: <strong class="text-primary">{{ $role->name }}</strong></h4>
                </div>

                <div class="">
                @forelse($groupedData as $groupName => $names)
                    <h4>{{ $groupName }}</h4>
                    <div class="mb-3">
                        @foreach($names as $name)
                            <strong class="badge bg-primary">{{ $name }}</strong>
                        @endforeach
                    </div>
                @empty
                    <div class="alert alert-warning text-center">
                        <strong>Sorry!</strong> Permission not found.
                    </div>
                @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
