@extends('layouts.app')
@section('content')
<div class="panel">
    <h1>المساهمة في هدية العيد</h1>
    <p class="privacy">تصل مساهمتك عبر الإدارة وفريق التوزيع دون كشف أسماء مستحقي الهدية أو بياناتهم.</p>
    <form method="post" action="{{ route('public.donation.store') }}">
        @csrf
        <div class="grid grid-2">
            <div class="field"><label>اسم المساهم (اختياري)</label><input name="donor_name" value="{{ old('donor_name') }}"></div>
            <div class="field"><label>رقم الهاتف</label><input name="donor_phone" value="{{ old('donor_phone') }}" required></div>
            <div class="field"><label>منطقتك</label><select name="donor_area_id"><option value="">اختياري</option>@foreach($areas as $area)<option value="{{ $area->id }}" @selected(old('donor_area_id') == $area->id)>{{ $area->name }}</option>@endforeach</select></div>
            <div class="field"><label>نطاق المساهمة</label><select name="donation_scope" required><option value="own_area">منطقتي</option><option value="selected_area">منطقة أخرى</option><option value="most_needed">الأكثر احتياجًا للتغطية</option></select></div>
            <div class="field"><label>المنطقة المختارة</label><select name="target_area_id"><option value="">اختر عند اختيار منطقة أخرى</option>@foreach($areas as $area)<option value="{{ $area->id }}" @selected(old('target_area_id') == $area->id)>{{ $area->name }}</option>@endforeach</select></div>
            <div class="field"><label>نوع المساهمة</label><select name="donation_type" required><option value="meat_kg">لحم بالكيلو</option><option value="money">مبلغ مالي</option><option value="sacrifice_share">سهم أضحية</option><option value="full_sacrifice">أضحية كاملة</option></select></div>
            <div class="field"><label>المبلغ</label><input name="amount" type="number" step="0.01" min="1" value="{{ old('amount') }}"></div>
            <div class="field"><label>كمية اللحم بالكيلو</label><input name="meat_kg" type="number" step="0.01" min="1" value="{{ old('meat_kg') }}"></div>
        </div>
        <div class="field"><label>عنوان الاستلام (اختياري)</label><textarea name="pickup_address">{{ old('pickup_address') }}</textarea></div>
        <input type="hidden" name="latitude" id="donor_latitude" value="{{ old('latitude') }}">
        <input type="hidden" name="longitude" id="donor_longitude" value="{{ old('longitude') }}">
        <input type="hidden" name="location_accuracy" id="donor_location_accuracy" value="{{ old('location_accuracy') }}">
        <button type="button" class="btn secondary" id="pickup-btn">تحديد موقعي الحالي للاستلام</button>
        <p class="hint" id="pickup-message">اختياري عند وجود استلام من موقعك. لن تكتب الإحداثيات يدويًا.</p>
        <div id="pickup-map" class="map"></div>
        <div class="field"><label>ملاحظات</label><textarea name="notes">{{ old('notes') }}</textarea></div>
        <div class="actions"><button type="submit">تسجيل المساهمة</button></div>
    </form>
</div>
@endsection
@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIINfQxn1/BK0mXOWr8fhA0PTp7aN6fB0g=" crossorigin="">
@endpush
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
let pickupMap, pickupMarker;
const dLat = document.getElementById('donor_latitude');
const dLng = document.getElementById('donor_longitude');
const dAcc = document.getElementById('donor_location_accuracy');
const pickupMsg = document.getElementById('pickup-message');

function setPickup(lat, lng, accuracy) {
    const position = [Number(lat), Number(lng)];
    dLat.value = position[0].toFixed(7);
    dLng.value = position[1].toFixed(7);
    if (accuracy !== undefined && accuracy !== null) dAcc.value = Math.round(accuracy);

    if (!pickupMarker) {
        pickupMarker = L.marker(position, {draggable: true}).addTo(pickupMap);
        pickupMarker.on('dragend', () => {
            const p = pickupMarker.getLatLng();
            setPickup(p.lat, p.lng, dAcc.value);
        });
    } else {
        pickupMarker.setLatLng(position);
    }

    pickupMap.setView(position, 17);
}

function initPickupMap() {
    const start = [Number(dLat.value || 21.4225), Number(dLng.value || 39.8262)];
    pickupMap = L.map('pickup-map').setView(start, dLat.value ? 17 : 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(pickupMap);
    if (dLat.value && dLng.value) setPickup(dLat.value, dLng.value, dAcc.value);
}

document.getElementById('pickup-btn').addEventListener('click', () => {
    if (!navigator.geolocation) {
        pickupMsg.textContent = 'المتصفح لا يدعم تحديد الموقع.';
        return;
    }
    pickupMsg.textContent = 'جاري تحديد موقع الاستلام...';
    navigator.geolocation.getCurrentPosition((pos) => {
        setPickup(pos.coords.latitude, pos.coords.longitude, pos.coords.accuracy);
        pickupMsg.textContent = 'تم تحديد موقع الاستلام ويمكن تعديل العلامة بالسحب.';
    }, () => {
        pickupMsg.textContent = 'تعذر تحديد الموقع. يمكنك تركه فارغًا وإضافة عنوان الاستلام.';
    }, {enableHighAccuracy: true, timeout: 15000});
});

initPickupMap();
</script>
@endpush
