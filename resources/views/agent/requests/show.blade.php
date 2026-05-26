@extends('layouts.app')
@section('content')
<div class="grid grid-2">
    <div class="panel">
        <h1>توصيل هدية العيد {{ $request->code }}</h1>
        <p><strong>الاسم الأول:</strong> {{ $request->first_name }}</p>
        <p><strong>الهاتف:</strong> <a href="tel:{{ $request->phone }}">{{ $request->phone }}</a></p>
        <p><strong>المنطقة:</strong> {{ $request->area?->name }}</p>
        <p><strong>العنوان:</strong> {{ $request->full_address }}</p>
        <p><strong>علامة قريبة:</strong> {{ $request->landmark ?? 'غير مذكورة' }}</p>
        <p><strong>الحالة:</strong> <x-status-badge :status="$request->status" /></p>
        <div class="actions">
            <a class="btn secondary" href="tel:{{ $request->phone }}">اتصال</a>
            <a class="btn" target="_blank" rel="noopener" href="{{ $request->mapsUrl() }}">فتح OpenStreetMap</a>
        </div>
        <div id="agent-beneficiary-map" class="map" style="margin-top:14px"></div>
        <p class="privacy">هذه البيانات أمانة لغرض التوصيل فقط، ولا يجوز تصويرها أو نشرها أو مشاركتها خارج فريق التوزيع المصرح.</p>
    </div>
    <div class="panel">
        <h2>تحديث التوصيل</h2>
        <form method="post" action="{{ route('agent.requests.status',$request) }}">
            @csrf
            <div class="field"><label>الحالة</label><select name="status"><option value="gift_received_by_agent">استلمت الهدية</option><option value="on_the_way">في الطريق</option><option value="delivered">تم التسليم</option><option value="failed">تعذر التسليم</option></select></div>
            <div class="field"><label>ملاحظة التوصيل</label><textarea name="note">{{ old('note', $request->agent_notes) }}</textarea></div>
            <button>حفظ التحديث</button>
        </form>
    </div>
</div>
@endsection
@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIINfQxn1/BK0mXOWr8fhA0PTp7aN6fB0g=" crossorigin="">
@endpush
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
const agentBeneficiaryPosition = [{{ $request->latitude }}, {{ $request->longitude }}];
const agentBeneficiaryMap = L.map('agent-beneficiary-map').setView(agentBeneficiaryPosition, 17);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom: 19, attribution: '&copy; OpenStreetMap'}).addTo(agentBeneficiaryMap);
L.marker(agentBeneficiaryPosition).addTo(agentBeneficiaryMap);
</script>
@endpush
