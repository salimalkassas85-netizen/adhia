@extends('layouts.app')
@section('content')
<div class="panel">
    <h1>استلام مساهمة {{ $donation->code }}</h1>
    <p><strong>المساهم:</strong> {{ $donation->donor_name ?? 'فاعل خير' }}</p>
    <p><strong>الهاتف:</strong> <a href="tel:{{ $donation->donor_phone }}">{{ $donation->donor_phone }}</a></p>
    <p><strong>نوع المساهمة:</strong> <x-donation-type :type="$donation->donation_type" /></p>
    <p><strong>عنوان الاستلام:</strong> {{ $donation->pickup_address ?? 'غير مذكور' }}</p>
    <p><strong>الحالة:</strong> <x-status-badge :status="$donation->status" /></p>
    @if($donation->pickupMapsUrl())
        <div class="actions">
            <a class="btn secondary" href="tel:{{ $donation->donor_phone }}">اتصال</a>
            <a class="btn" target="_blank" rel="noopener" href="{{ $donation->pickupMapsUrl() }}">فتح OpenStreetMap</a>
        </div>
        <div id="agent-pickup-map" class="map" style="margin-top:14px"></div>
    @else
        <p class="notice">لم يحدد المساهم موقع استلام على الخريطة.</p>
    @endif
    <p class="privacy">بيانات المساهم أمانة لغرض الاستلام فقط، ولا تظهر لمستحقي هدية العيد.</p>
</div>
@endsection
@if($donation->pickupMapsUrl())
@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIINfQxn1/BK0mXOWr8fhA0PTp7aN6fB0g=" crossorigin="">
@endpush
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
const agentPickupPosition = [{{ $donation->latitude }}, {{ $donation->longitude }}];
const agentPickupMap = L.map('agent-pickup-map').setView(agentPickupPosition, 17);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom: 19, attribution: '&copy; OpenStreetMap'}).addTo(agentPickupMap);
L.marker(agentPickupPosition).addTo(agentPickupMap);
</script>
@endpush
@endif
