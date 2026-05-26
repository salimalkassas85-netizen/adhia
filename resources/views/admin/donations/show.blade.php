@extends('layouts.app')
@php
    $donationStatuses = \App\Support\ArabicLabels::donationStatusOptions();
    $beneficiary = $donation->allocations->first()?->beneficiaryRequest;
@endphp
@section('content')
<div class="grid grid-2">
    <div class="panel">
        <h1>طلب توصيل {{ $donation->code }}</h1>
        <p class="notice">هذا الطلب يديره أدمن المنطقة مباشرة: استلام من المتبرع ثم تسليم للمحتاج.</p>
        <h2>بيانات الاستلام من المتبرع</h2>
        <p><strong>الاسم:</strong> {{ $donation->donor_name ?? 'فاعل خير' }}</p>
        <p><strong>الهاتف:</strong> <a href="tel:{{ $donation->donor_phone }}">{{ $donation->donor_phone }}</a></p>
        <p><strong>المنطقة:</strong> {{ $donation->donorArea?->name ?? 'غير محددة' }}</p>
        <p><strong>نوع المساهمة:</strong> <x-donation-type :type="$donation->donation_type" /></p>
        <p><strong>المبلغ:</strong> {{ $donation->amount ?? '-' }} | <strong>اللحم:</strong> {{ $donation->meat_kg ?? '-' }} كجم</p>
        <p><strong>عنوان الاستلام:</strong> {{ $donation->pickup_address ?? 'غير مذكور' }}</p>
        @if($donation->pickupMapsUrl())
            <a class="btn secondary" target="_blank" rel="noopener" href="{{ $donation->pickupMapsUrl() }}">فتح موقع الاستلام في OpenStreetMap</a>
            <div id="donor-pickup-map" class="map" style="margin-top:14px"></div>
        @endif
    </div>

    <div class="panel">
        <h2>بيانات التسليم للمحتاج</h2>
        @if($beneficiary)
            <p><strong>رمز الطلب:</strong> {{ $beneficiary->code }}</p>
            <p><strong>الاسم الأول:</strong> {{ $beneficiary->first_name }}</p>
            <p><strong>الهاتف:</strong> <a href="tel:{{ $beneficiary->phone }}">{{ $beneficiary->phone }}</a></p>
            <p><strong>المنطقة:</strong> {{ $beneficiary->area?->name }}</p>
            <p><strong>العنوان:</strong> {{ $beneficiary->full_address }}</p>
            <p><strong>علامة قريبة:</strong> {{ $beneficiary->landmark ?? 'غير مذكورة' }}</p>
            @if($beneficiary->latitude && $beneficiary->longitude)
                <a class="btn secondary" target="_blank" rel="noopener" href="{{ $beneficiary->mapsUrl() }}">فتح موقع التسليم في OpenStreetMap</a>
                <div id="beneficiary-map" class="map" style="margin-top:14px"></div>
            @endif
        @else
            <p class="privacy">لم يتم ربط هذه المساهمة بطلب هدية بعد.</p>
        @endif
    </div>
</div>

<div class="panel" style="margin-top:18px">
    <h2>حالة الطلب الواحدة</h2>
    <p><strong>أدمن المنطقة المسؤول:</strong> {{ $donation->assignedAdmin?->name ?? auth()->user()->name }}</p>
    <p><strong>الحالة الحالية:</strong> <x-status-badge :status="$donation->status" /></p>

    @if($donation->allocations->isEmpty())
        <form method="post" action="{{ route('admin.donations.allocate',$donation) }}">@csrf
            <div class="field"><label>المنطقة</label><select name="area_id">@foreach($areas as $area)<option value="{{ $area->id }}">{{ $area->name }}</option>@endforeach</select></div>
            <div class="field"><label>طلب هدية واحد</label><select name="beneficiary_request_id" required>@foreach($requests as $request)<option value="{{ $request->id }}">{{ $request->code }} - {{ $request->first_name }} - {{ $request->area?->name }}</option>@endforeach</select></div>
            <button>ربط المساهمة بطلب هدية واحد</button>
        </form>
        <hr>
    @endif

    <form method="post" action="{{ route('admin.donations.status',$donation) }}">@csrf
        <div class="field">
            <label>الحالة</label>
            <select name="status">
                @foreach($donationStatuses as $status => $label)
                    <option value="{{ $status }}" @selected($donation->status === $status)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="field"><label>ملاحظة</label><textarea name="note"></textarea></div>
        <button>تحديث حالة الطلب</button>
    </form>
</div>

<div class="panel" style="margin-top:18px">
    <h2>سجل الحالة</h2>
    @forelse($donation->statusLogs as $log)
        <p>{{ $log->created_at->format('Y-m-d H:i') }}: {{ $log->from_status ? \App\Support\ArabicLabels::status($log->from_status) : 'بداية' }} ← {{ \App\Support\ArabicLabels::status($log->to_status) }} بواسطة {{ $log->user?->name ?? 'النظام' }} {{ $log->note }}</p>
    @empty
        <p class="privacy">لا يوجد سجل بعد.</p>
    @endforelse
</div>
@endsection
@if($donation->pickupMapsUrl() || ($beneficiary && $beneficiary->latitude && $beneficiary->longitude))
@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIINfQxn1/BK0mXOWr8fhA0PTp7aN6fB0g=" crossorigin="">
@endpush
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
@if($donation->pickupMapsUrl())
<script>
const donorPickupPosition = [{{ $donation->latitude }}, {{ $donation->longitude }}];
const donorPickupMap = L.map('donor-pickup-map').setView(donorPickupPosition, 17);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom: 19, attribution: '&copy; OpenStreetMap'}).addTo(donorPickupMap);
L.marker(donorPickupPosition).addTo(donorPickupMap);
</script>
@endif
@if($beneficiary && $beneficiary->latitude && $beneficiary->longitude)
<script>
const beneficiaryPosition = [{{ $beneficiary->latitude }}, {{ $beneficiary->longitude }}];
const beneficiaryMap = L.map('beneficiary-map').setView(beneficiaryPosition, 17);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom: 19, attribution: '&copy; OpenStreetMap'}).addTo(beneficiaryMap);
L.marker(beneficiaryPosition).addTo(beneficiaryMap);
</script>
@endif
@endpush
@endif
