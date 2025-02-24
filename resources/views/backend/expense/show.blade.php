<div class="card">
    <div class="card-body">
        <div  class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>{{ $expense->expenseCategory->name }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Title</td>
                        <td>{{ $expense->title }}</td>
                    </tr>
                    <tr>
                        <td>Description</td>
                        <td>{{ $expense->description ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Amount</td>
                        <td>{{ get_site_settings('site_currency_symbol') }}{{ $expense->amount }}</td>
                    </tr>
                    <tr>
                        <td>Date</td>
                        <td>{{ date('d M, Y', strtotime($expense->expense_date)) }}</td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>
                            @if($expense->status == 'Active')
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Created By</td>
                        <td>{{ $expense->createdBy->name }}</td>
                    </tr>
                    <tr>
                        <td>Created At</td>
                        <td>{{ $expense->created_at->format('d M, Y') }}</td>
                    </tr>
                    <tr>
                        <td>Updated By</td>
                        <td>{{ $expense->updatedBy->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Updated At</td>
                        <td>{{ date('d M, Y', strtotime($expense->updated_at)) }}</td>
                    </tr>
                    @if ($expense->deleted_at)
                        <tr>
                            <td>Deleted By</td>
                            <td>{{ $expense->deletedBy->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td>Deleted At</td>
                            <td>{{ $expense->deleted_at ? date('d M, Y', strtotime($expense->deleted_at)) : 'N/A' }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

    </div>
</div>

