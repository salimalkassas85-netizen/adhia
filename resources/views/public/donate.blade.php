@extends('layouts.app')
@section('content')
<div class="panel">
    <h1>المساهمة في هدية العيد</h1>
    <p class="privacy">تصل مساهمتك عبر أدمن المنطقة المسؤول دون كشف بيانات المحتاج للمتبرع.</p>
    <form method="post" action="{{ route('public.donation.store') }}">
        @csrf
        <div class="grid grid-2">
            <div class="field"><label>اسم المساهم (اختياري)</label><input name="donor_name" value="{{ old('donor_name') }}"></div>
            <div class="field"><label>رقم الهاتف</label><input name="donor_phone" value="{{ old('donor_phone') }}" required></div>
            <div class="field"><label>منطقتك</label><select name="donor_area_id" required><option value="">اختر المنطقة</option>@foreach($areas as $area)<option value="{{ $area->id }}" @selected(old('donor_area_id') == $area->id)>{{ $area->name }}</option>@endforeach</select></div>
            <input type="hidden" name="donation_scope" value="own_area">
            <div class="field"><label>نطاق المساهمة</label><input value="منطقتي فقط" disabled></div>
            <div class="field"><label>نوع المساهمة</label><select name="donation_type" required><option value="meat_kg">لحم بالكيلو</option><option value="money">مبلغ مالي</option><option value="sacrifice_share">سهم أضحية</option><option value="full_sacrifice">أضحية كاملة</option></select></div>
            <div class="field"><label>المبلغ</label><input name="amount" type="number" step="0.01" min="1" value="{{ old('amount') }}"></div>
            <div class="field"><label>كمية اللحم بالكيلو</label><input name="meat_kg" type="number" step="0.01" min="1" value="{{ old('meat_kg') }}"></div>
        </div>

        <div id="cases-container" class="panel" style="display:none; margin-bottom:14px; background:var(--soft);">
            <div class="actions" style="justify-content:space-between;">
                <h2>الحالات المحتاجة في هذه المنطقة</h2>
                <span class="badge" id="cases-count">0 حالة</span>
            </div>
            <p class="privacy">يمكنك اختيار حالة واحدة فقط لتوجيه مساهمتك لها مباشرة. إذا لم تختر أحداً، سيتم توزيع المساهمة داخل منطقتك.</p>
            <div id="cases-list" class="grid grid-2"></div>
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

const donorAreaSelect = document.querySelector('select[name="donor_area_id"]');
const casesContainer = document.getElementById('cases-container');
const casesList = document.getElementById('cases-list');
const casesCount = document.getElementById('cases-count');

function fetchCases() {
    let areaId = null;
    areaId = donorAreaSelect.value;
    
    if (!areaId) {
        casesContainer.style.display = 'none';
        casesList.innerHTML = '';
        return;
    }

    casesList.innerHTML = '<p class="privacy">جاري البحث عن حالات مستحقة...</p>';
    casesContainer.style.display = 'block';

    fetch(`/api/areas/${areaId}/cases`)
        .then(res => res.json())
        .then(cases => {
            casesCount.textContent = `${cases.length} حالة`;
            if (cases.length === 0) {
                casesList.innerHTML = '<p class="privacy">لا توجد طلبات انتظار في هذه المنطقة حالياً. سيتم حفظ مساهمتك كرصيد للمنطقة للحالات القادمة.</p>';
                return;
            }

            casesList.innerHTML = cases.map(c => `
                <label class="card" style="display:block; cursor:pointer; background:#fff;">
                    <input type="radio" name="selected_case_id" value="${c.id}" style="width:auto; margin-left:8px;">
                    <strong>مستفيد #${c.id}</strong>
                    <div style="font-size:13px; color:var(--muted); margin-top:4px;">
                        <div>الأسرة: ${c.family_members_count} فرد</div>
                        <div>الحالة: ${c.social_status}</div>
                        <div>${c.has_children ? '✅ يوجد أطفال' : '❌ لا يوجد أطفال'} | ${c.has_elderly ? '✅ كبار سن' : '❌ لا يوجد كبار سن'}</div>
                        <div style="margin-top:6px; color:var(--gold);">📦 تم تخصيص ${c.received_donations_count} تبرع له حتى الآن</div>
                    </div>
                </label>
            `).join('');
        })
        .catch(() => {
            casesList.innerHTML = '<p class="errors">حدث خطأ أثناء جلب الحالات.</p>';
        });
}

donorAreaSelect.addEventListener('change', fetchCases);

// Initial fetch if values are pre-selected
fetchCases();

</script>
@endpush
