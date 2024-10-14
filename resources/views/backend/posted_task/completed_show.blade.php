<div class="row">
    <div class="col-lg-6">
        <div class="table-responsive">
            <table class="table table-bordered">
                <tr>
                    <th>Task Id</th>
                    <td>{{ $postTask->id }}</td>
                </tr>
                <tr>
                    <th>Task Category</th>
                    <td>{{ $postTask->category->name }}</td>
                </tr>
                <tr>
                    <th>Task Sub Category</th>
                    <td>{{ $postTask->subCategory->name }}</td>
                </tr>
                @if ($postTask->child_category_id )
                <tr>
                    <th>Task Child Category</th>
                    <td>{{ $postTask->childCategory->name }}</td>
                </tr>
                @endif
                @if ($postTask->thumbnail)
                <tr>
                    <th>Task Thumbnail</th>
                    <td>
                        <img src="{{ asset('uploads/task_thumbnail_photo') }}/{{ $postTask->thumbnail }}" alt="Task Thumbnail" class="img-fluid">
                    </td>
                </tr>
                @endif
                <tr>
                    <th>Task Title</th>
                    <td>{{ $postTask->title }}</td>
                </tr>
                <tr>
                    <th>Task Description</th>
                    <td>{{ $postTask->description }}</td>
                </tr>
                <tr>
                    <th>Task Required Proof</th>
                    <td>{{ $postTask->required_proof }}</td>
                </tr>
                <tr>
                    <th>Task Additional Note</th>
                    <td>{{ $postTask->additional_note }}</td>
                </tr>
                <tr>
                    <th>Task Created At</th>
                    <td>{{ $postTask->created_at->format('D d-M-Y h:i:s A') }}</td>
                </tr>
                <tr>
                    <th>Approved By</th>
                    <td>{{ $postTask->approvedBy->name }}</td>
                </tr>
                <tr>
                    <th>Approved At</th>
                    <td>{{ date('D d-M-Y h:i:s A', strtotime($postTask->approved_at)) }}</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="table-responsive">
            <table class="table table-bordered">
                <tr>
                    <th>User</th>
                    <td>{{ $postTask->user->name }}</td>
                </tr>
                <tr>
                    <th>User Email</th>
                    <td>{{ $postTask->user->email }}</td>
                </tr>
                <tr>
                    <th>Work Needed</th>
                    <td>{{ $postTask->work_needed }}</td>
                </tr>
                <tr>
                    <th>Earnings From Work</th>
                    <td>{{ $postTask->earnings_from_work }} {{ get_site_settings('site_currency_symbol') }}</td>
                </tr>
                <tr>
                    <th>Extra Screenshots</th>
                    <td>{{ $postTask->extra_screenshots }}</td>
                </tr>
                <tr>
                    <th>Boosted Time</th>
                    <td>
                        @if($postTask->boosted_time < 60)
                            {{ $postTask->boosted_time }} minute{{ $postTask->boosted_time > 1 ? 's' : '' }}
                        @elseif($postTask->boosted_time >= 60)
                            {{ round($postTask->boosted_time / 60, 1) }} hour{{ round($postTask->boosted_time / 60, 1) > 1 ? 's' : '' }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Running Day</th>
                    <td>{{ $postTask->running_day }} Days</td>
                </tr>
                <tr>
                    <th>Task Charge</th>
                    <td>{{ $postTask->charge }} {{ get_site_settings('site_currency_symbol') }}</td>
                </tr>
                <tr>
                    <th>Site Charge <span class="text-info">( {{ get_default_settings('task_posting_charge_percentage') }} % )</span></th>
                    <td>{{ $postTask->site_charge }} {{ get_site_settings('site_currency_symbol') }}</td>
                </tr>
                <tr>
                    <th>Total Charge</th>
                    <td>{{ $postTask->total_charge }} {{ get_site_settings('site_currency_symbol') }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
