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
                    <th>Task Required Proof Answer</th>
                    <td>{{ $postTask->required_proof_answer }}</td>
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
                    <td>
                        Required Proof Photo
                    </td>
                    <td>
                        Free: 1 + Additional: {{ $postTask->required_proof_photo - 1 }} = Total: {{ $postTask->required_proof_photo }} Required Proof Photo{{ $postTask->required_proof_photo > 1 ? 's' : '' }} <br>
                        <span class="text-primary">( Charge: {{ $postTask->required_proof_photo - 1 }} * {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('task_posting_additional_required_proof_photo_charge') }} = {{ get_site_settings('site_currency_symbol') }} {{ ($postTask->required_proof_photo - 1) * get_default_settings('task_posting_additional_required_proof_photo_charge') }} )</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        Boosted Time <br>
                        <span class="text-info">Every 15 minutes boost Charges {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('task_posting_boosted_time_charge') }}. <br>
                        When the task is boosted, it will be shown at the top of the task list.</span>
                    </td>
                    <td>
                        @if($postTask->boosted_time < 60)
                            {{ $postTask->boosted_time }} Minute{{ $postTask->boosted_time > 1 ? 's' : '' }} <br>
                        @elseif($postTask->boosted_time >= 60)
                            {{ round($postTask->boosted_time / 60, 1) }} Hour{{ round($postTask->boosted_time / 60, 1) > 1 ? 's' : '' }} <br>
                        @endif
                        <span class="text-primary">( Charge: {{ $postTask->boosted_time }} * {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('task_posting_boosted_time_charge') }} = {{ get_site_settings('site_currency_symbol') }} {{ $postTask->boosted_time / 15 * get_default_settings('task_posting_boosted_time_charge') }} )</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        Work Duration <br>
                        <span class="text-info">When work duration is over the task will be canceled automatically.</span>
                    </td>
                    <td>
                        Free: 3 Days + Additional: {{ $postTask->work_duration - 3 }} Days = Total: {{ $postTask->work_duration }} Days <br>
                        <span class="text-primary">( Charge: {{ $postTask->work_duration - 3 }} * {{ get_site_settings('site_currency_symbol') }} {{ get_default_settings('task_posting_additional_work_duration_charge') }} = {{ get_site_settings('site_currency_symbol') }} {{ ($postTask->work_duration - 3) * get_default_settings('task_posting_additional_work_duration_charge') }} )</span>
                    </td>
                </tr>
                <tr>
                    <td>Task Charge</td>
                    <td>
                        <span class="text-primary">( {{ $postTask->work_needed }} * {{ get_site_settings('site_currency_symbol') }} {{ $postTask->earnings_from_work }} ) + {{ get_site_settings('site_currency_symbol') }} {{ ($postTask->required_proof_photo - 1) * get_default_settings('task_posting_additional_required_proof_photo_charge') }} + {{ get_site_settings('site_currency_symbol') }} {{ $postTask->boosted_time / 15 * get_default_settings('task_posting_boosted_time_charge') }} + {{ get_site_settings('site_currency_symbol') }} {{ ($postTask->work_duration - 3) * get_default_settings('task_posting_boosted_time_charge') }} = {{ get_site_settings('site_currency_symbol') }} {{ $postTask->charge }}</span>
                    </td>
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
