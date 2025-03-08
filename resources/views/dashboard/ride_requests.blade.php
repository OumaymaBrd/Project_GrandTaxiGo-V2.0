@foreach($rideRequests as $request)
<div class="card mb-3">
    <!-- Card content -->

    <div class="card-footer">
        <div class="d-flex justify-content-end gap-2">
            <!-- Other buttons -->

            @if($request->status == 'accepted' || $request->status == 'pending')
            <a href="{{ route('chat.ride', ['rideId' => $request->id]) }}" class="btn btn-info btn-sm">
                <i class="fas fa-comment me-2"></i>Message
            </a>
            @endif
        </div>
    </div>
</div>
@endforeach

