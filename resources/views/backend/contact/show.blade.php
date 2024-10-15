<div class="card">
    <div class="card-body">
        <h5 class="mb-2"><strong>Name: </strong>{{ $contact->name }}</h5>
        <h5 class="mb-2"><strong>Email: </strong>{{ $contact->email }}</h5>
        <h5 class="mb-2"><strong>Phone: </strong>{{ $contact->phone ?? 'N/A' }}</h5>
        <h5 class="mb-2"><strong>Subject: </strong>{{ $contact->subject }}</h5>
        <h5 class="mb-2"><strong>Message: </strong>{{ $contact->message }}</h5>
        <h5 class="mb-2"><strong>Status: </strong><span class="badge bg-{{ $contact->status == 'Unread' ? 'danger' : 'success' }}">{{ $contact->status }}</span></h5>
        <h5 class="mb-2"><strong>Submit At: </strong>{{ $contact->created_at->format('d-F-Y h:i:s A') }}</h5>
    </div>
</div>

