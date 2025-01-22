@extends('layouts.template_master')

@section('title', 'Employee Details')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Employee Profile Details - Id: {{ $employee->id }}</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Profile Photo</th>
                                <th>
                                    <img src="{{ asset('uploads/profile_photo') }}/{{ $employee->profile_photo }}" alt="Profile Photo" class="img-thumbnail" width="100">
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Employee Name</td>
                                <td>{{ $employee->name }}</td>
                            </tr>
                            <tr>
                                <td>Username</td>
                                <td>{{ $employee->username ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td>{{ $employee->email }}</td>
                            </tr>
                            <tr>
                                <td>Date of Birth</td>
                                <td>{{ $employee->date_of_birth ? date('d M, Y', strtotime($employee->date_of_birth)) : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Gender</td>
                                <td>{{ $employee->gender ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Phone</td>
                                <td>{{ $employee->phone ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Address</td>
                                <td>{{ $employee->address ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Role</td>
                                <td>
                                    @foreach ($employee->roles as $role)
                                        <span class="badge bg-primary">{{ $role->name }}</span>
                                    @endforeach
                                </td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>
                                    @if ($employee->status == 'Active')
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>Bio</td>
                                <td>{{ $employee->bio ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Last Activity At</td>
                                <td>{{ $employee->last_activity_at ? date('d M, Y  h:i:s A', strtotime($employee->last_activity_at)) : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Created By</td>
                                <td>{{ $employee->created_by ? $employee->createdBy->name : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Updated By</td>
                                <td>{{ $employee->updated_by ? $employee->updatedBy->name : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Deleted By</td>
                                <td>{{ $employee->deleted_by ? $employee->deletedBy->name : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Created At</td>
                                <td>{{ $employee->created_at->format('d M, Y h:i:s A') }}</td>
                            </tr>
                            <tr>
                                <td>Updated At</td>
                                <td>{{ $employee->updated_at->format('d M, Y h:i:s A') }}</td>
                            </tr>
                            <tr>
                                <td>Deleted At</td>
                                <td>{{ $employee->deleted_at ? $employee->deleted_at->format('d M, Y h:i:s A') : 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
    });
</script>
@endsection

