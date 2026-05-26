@extends('layouts.app')
@php
    $donationStatuses = ['pending','confirmed','received','allocated','in_distribution','completed','cancelled'];
@endphp
@section('content')
<div class="grid grid-2">
    <div class="panel">
        <h1>مساهمة {{ $donation->code }}</h1>
        <p><strong>الاسم:</strong> {{ $donation->donor_name ?? 'فاعل خير' }}</p>
        <p><strong>الهاتف:</strong> <a href="tel:{{ $donation->donor_phone }}">{{ $donation->donor_phone }}</a></p>
        <p><strong>منطقة المساهم:</strong> {{ $donation->donorArea?->name ?? 'غير محددة' }}</p>
        <p><strong>منطقة التوزيع:</strong> {{ $donation->targetArea?->name ?? 'تحددها الإدارة' }}</p>
        <p><strong>نوع المساهمة:</strong> <x-donation-type :type="$donation->donation_type" /></p>
        <p><strong>المبلغ:</strong> {{ $donation->amount ?? '-' }} | <strong>اللحم:</strong> {{ $donation->meat_kg ?? '-' }} كجم</p>
        <p><strong>الحالة:</strong> <x-status-badge :status="$donation->status" /></p>
        <p><strong>عنوان الاستلام:</strong> {{ $donation->pickup_address ?? 'غير مذكور' }}</p>
        @if($donation->pickupMapsUrl())
            <a class="btn secondary" target="_blank" rel="noopener" href="{{ $donation->pickupMapsUrl() }}">فتح موقع الاستلام في OpenStreetMap</a>
            <div id="donor-pickup-map" class="map" style="margin-top:14px"></div>
        @endif
    </div>
    <div class="panel">
        <h2>إجراءات المساهمة</h2>
        <form method="post" action="{{ route('admin.donations.confirm',$donation) }}">@csrf<button>تأكيد</button></form>
        <form method="post" action="{{ route('admin.donations.receive',$donation) }}" style="margin-top:10px">@csrf<button>تسجيل الاستلام</button></form>
        <hr>
        <form method="post" action="{{ route('admin.donations.assign-pickup',$donation) }}">@csrf
            <div class="field"><label>إسناد استلام المساهمة إلى</label><select name="pickup_agent_id">@foreach($agents as $agent)<option value="{{ $agent->id }}" @selected($donation->pickup_agent_id === $agent->id)>{{ $agent->name }}</option>@endforeach</select></div>
            <button>إسناد الاستلام</button>
        </form>
        <hr>
        <form method="post" action="{{ route('admin.donations.allocate',$donation) }}">@csrf
            <div class="field"><label>المنطقة</label><select name="area_id">@foreach($areas as $area)<option value="{{ $area->id }}">{{ $area->name }}</option>@endforeach</select></div>
            <div class="field"><label>طلب هدية العيد</label><select name="beneficiary_request_id">@foreach($requests as $request)<option value="{{ $request->id }}">{{ $request->code }} - {{ $request->area?->name }}</option>@endforeach</select></div>
            <button>تخصيص / نقل تغطية</button>
        </form>
        <hr>
        <form method="post" action="{{ route('admin.donations.status',$donation) }}">@csrf
            <div class="field">
                <label>الحالة</label>
                <select name="status">
                    @foreach($donationStatuses as $status)
                        <option value="{{ $status }}" @selected($donation->status === $status)>{{ \App\Support\ArabicLabels::status($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field"><label>ملاحظة</label><textarea name="note"></textarea></div>
            <button>تحديث</button>
        </form>
    </div>
</div>
<div class="panel" style="margin-top:18px">
    <h2>التخصيصات وسجل الحالة</h2>
    @foreach($donation->allocations as $allocation)
        <p>{{ $allocation->beneficiaryRequest?->code }} - {{ $allocation->area?->name }} - {{ \App\Support\ArabicLabels::status($allocation->status) }}</p>
    @endforeach
    @foreach($donation->statusLogs as $log)
        <p>{{ $log->created_at->format('Y-m-d H:i') }}: {{ $log->from_status ? \App\Support\ArabicLabels::status($log->from_status) : 'بداية' }} ← {{ \App\Support\ArabicLabels::status($log->to_status) }}</p>
    @endforeach
</div>
@endsection
@if($donation->pickupMapsUrl())
@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIINfQxn1/BK0mXOWr8fhA0PTp7aN6fB0g=" crossorigin="">
@endpush
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
const donorPickupPosition = [{{ $donation->latitude }}, {{ $donation->longitude }}];
const donorPickupMap = L.map('donor-pickup-map').setView(donorPickupPosition, 17);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom: 19, attribution: '&copy; OpenStreetMap'}).addTo(donorPickupMap);
L.marker(donorPickupPosition).addTo(donorPickupMap);
</script>
@endpush
@endif
