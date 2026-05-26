@extends('layouts.app')
@section('content')
<div class="panel">
    <h1>طلب هدية العيد بسرية وستر</h1>
    <p class="privacy">بيانات موقعك تُستخدم فقط لتوصيل هدية العيد بواسطة فريق معتمد، ولا تظهر لأي شخص خارج فريق التوزيع.</p>
    <form method="post" action="{{ route('public.request.store') }}" id="gift-form">
        @csrf
        <div class="grid grid-2">
            <div class="field"><label>الاسم الأول</label><input name="first_name" value="{{ old('first_name') }}" maxlength="50" required></div>
            <div class="field"><label>رقم الهاتف</label><input name="phone" value="{{ old('phone') }}" maxlength="20" required></div>
            <div class="field"><label>المنطقة</label><select name="area_id" required><option value="">اختر المنطقة</option>@foreach($areas as $area)<option value="{{ $area->id }}" @selected(old('area_id') == $area->id)>{{ $area->name }}</option>@endforeach</select></div>
            <div class="field"><label>عدد أفراد الأسرة</label><input name="family_members_count" type="number" min="1" max="50" value="{{ old('family_members_count') }}"></div>
        </div>
        <div class="grid grid-2">
            <label><input style="width:auto" type="checkbox" name="has_children" value="1" @checked(old('has_children'))> يوجد أطفال</label>
            <label><input style="width:auto" type="checkbox" name="has_elderly" value="1" @checked(old('has_elderly'))> يوجد كبار سن</label>
        </div>
        <div class="field"><label>العنوان الكامل</label><textarea name="full_address" required>{{ old('full_address') }}</textarea></div>
        <div class="field"><label>علامة قريبة</label><input name="landmark" value="{{ old('landmark') }}"></div>
        <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
        <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
        <input type="hidden" name="location_accuracy" id="location_accuracy" value="{{ old('location_accuracy') }}">
        <div class="field">
            <button type="button" class="btn secondary" id="locate-btn">تحديد موقعي الحالي</button>
            <p class="hint" id="location-message">لن يُطلب منك كتابة الإحداثيات. اضغط الزر ثم حرّك العلامة إذا احتجت لتعديل بسيط.</p>
        </div>
        <div id="map" class="map"></div>
        <div class="actions"><button type="submit">إرسال الطلب بأمان</button></div>
    </form>
</div>
@endsection
@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIINfQxn1/BK0mXOWr8fhA0PTp7aN6fB0g=" crossorigin="">
@endpush
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
let map, marker;
const latInput = document.getElementById('latitude');
const lngInput = document.getElementById('longitude');
const accuracyInput = document.getElementById('location_accuracy');
const message = document.getElementById('location-message');

function setPosition(lat, lng, accuracy) {
    const position = [Number(lat), Number(lng)];
    latInput.value = position[0].toFixed(7);
    lngInput.value = position[1].toFixed(7);
    if (accuracy !== undefined && accuracy !== null) accuracyInput.value = Math.round(accuracy);

    if (!marker) {
        marker = L.marker(position, {draggable: true}).addTo(map);
        marker.on('dragend', () => {
            const p = marker.getLatLng();
            setPosition(p.lat, p.lng, accuracyInput.value);
        });
    } else {
        marker.setLatLng(position);
    }

    map.setView(position, 17);
}

function initMap() {
    const start = [Number(latInput.value || 21.4225), Number(lngInput.value || 39.8262)];
    map = L.map('map').setView(start, latInput.value ? 17 : 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);
    if (latInput.value && lngInput.value) setPosition(latInput.value, lngInput.value, accuracyInput.value);
}

document.getElementById('locate-btn').addEventListener('click', () => {
    if (!navigator.geolocation) {
        message.textContent = 'المتصفح لا يدعم تحديد الموقع. جرّب من هاتفك أو متصفح حديث.';
        return;
    }
    message.textContent = 'جاري تحديد الموقع بأمان...';
    navigator.geolocation.getCurrentPosition((pos) => {
        setPosition(pos.coords.latitude, pos.coords.longitude, pos.coords.accuracy);
        message.textContent = 'تم تحديد الموقع. يمكنك تحريك العلامة على الخريطة لتدقيق نقطة التوصيل.';
    }, () => {
        message.textContent = 'تعذر تحديد الموقع. تأكد من السماح للمتصفح باستخدام الموقع.';
    }, {enableHighAccuracy: true, timeout: 15000});
});

document.getElementById('gift-form').addEventListener('submit', (event) => {
    if (!latInput.value || !lngInput.value) {
        event.preventDefault();
        message.textContent = 'يرجى تحديد موقع التوصيل قبل إرسال الطلب.';
        message.scrollIntoView({behavior: 'smooth', block: 'center'});
    }
});

initMap();
</script>
@endpush
