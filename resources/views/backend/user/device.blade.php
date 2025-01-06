
<div class="card">
    <div class="card-body d-flex justify-content-between align-items-center">
        <h4 class="card-title">Device Information</h4>
        <h5>Total: {{ $userDevices->count() }}</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Sl No</th>
                        <th>Ip</th>
                        <th>Device Type</th>
                        <th>Browser</th>
                        <th>Updated Time</th>
                        <th>Same Ip Users</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($userDevices as $device)
                    <tr>
                        <td>{{ $loop->index+1 }}</td>
                        <td>{{ $device->ip }}</td>
                        <td>{{ $device->device_type }}</td>
                        <td>{{ $device->browser }}</td>
                        <td>{{ date('d M, Y h:i:s A', strtotime($device->updated_at)) }}</td>
                        <td>
                            @php
                                $sameIpUserIds = App\Models\UserDevice::where('ip', $device->ip)
                                    ->where('user_id', '!=', $device->user_id)
                                    ->groupBy('user_id')
                                    ->pluck('user_id')
                                    ->toArray();
                                $sameIpUsers = App\Models\User::whereIn('id', $sameIpUserIds)
                                    ->whereIn('status', ['Active', 'Blocked'])
                                    ->where('user_type', 'Frontend')
                                    ->get();
                            @endphp
                            @forelse ($sameIpUsers as $user)
                                <a href="{{ route('backend.user.show', encrypt($user->id)) }}" class="text-primary" target="_blank">{{ $user->id }} - {{ $user->name }}</a> <br>
                            @empty
                                <span class="text-danger">No user found</span>
                            @endforelse
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

