@extends('layouts.app')
@php
    $requestStatuses = \App\Support\ArabicLabels::beneficiaryStatusOptions();
@endphp
@section('content')
<div class="grid grid-2">
    <div class="panel">
        <h1>طلب {{ $request->code }}</h1>
        <p><strong>الاسم الأول:</strong> {{ $request->first_name }}</p>
        <p><strong>الهاتف:</strong> <a href="tel:{{ $request->phone }}">{{ $request->phone }}</a></p>
        <p><strong>المنطقة:</strong> {{ $request->area?->name }}</p>
        <p><strong>الحالة:</strong> <x-status-badge :status="$request->status" /></p>
        <p><strong>عدد أفراد الأسرة:</strong> {{ $request->family_members_count ?? 'غير محدد' }}</p>
        <p><strong>أطفال:</strong> {{ $request->has_children ? 'نعم' : 'لا' }} | <strong>كبار سن:</strong> {{ $request->has_elderly ? 'نعم' : 'لا' }}</p>
        <p><strong>العنوان:</strong> {{ $request->full_address }}</p>
        <p><strong>علامة قريبة:</strong> {{ $request->landmark ?? 'غير مذكورة' }}</p>
        <a class="btn secondary" target="_blank" rel="noopener" href="{{ $request->mapsUrl() }}">فتح الموقع في OpenStreetMap</a>
        <div id="beneficiary-map" class="map" style="margin-top:14px"></div>
    </div>
    <div class="panel">
        <h2>إجراءات الإدارة</h2>
        <form method="post" action="{{ route('admin.beneficiary-requests.approve',$request) }}">@csrf<button>اعتماد الطلب</button></form>
        <hr>
        <form method="post" action="{{ route('admin.beneficiary-requests.assign',$request) }}">@csrf
            <div class="field"><label>إسناد إلى</label><select name="assigned_agent_id" required>@foreach($agents as $agent)<option value="{{ $agent->id }}">{{ $agent->name }}</option>@endforeach</select></div>
            <div class="field"><label>ملاحظة إدارية</label><textarea name="admin_notes">{{ old('admin_notes',$request->admin_notes) }}</textarea></div>
            <button>إسناد</button>
        </form>
        <hr>
        <form method="post" action="{{ route('admin.beneficiary-requests.status',$request) }}">@csrf
            <div class="field">
                <label>تحديث الحالة</label>
                <select name="status">
                    @foreach($requestStatuses as $status => $label)
                        <option value="{{ $status }}" @selected($request->status === $status)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field"><label>ملاحظة</label><textarea name="note"></textarea></div>
            <button>تحديث</button>
        </form>
    </div>
</div>
<div class="panel" style="margin-top:18px">
    <h2>سجل الحالة</h2>
    @foreach($request->statusLogs as $log)
        <p>{{ $log->created_at->format('Y-m-d H:i') }}: {{ $log->from_status ? \App\Support\ArabicLabels::status($log->from_status) : 'بداية' }} ← {{ \App\Support\ArabicLabels::status($log->to_status) }} بواسطة {{ $log->user?->name ?? 'النظام' }} {{ $log->note }}</p>
    @endforeach
</div>
@endsection
@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIINfQxn1/BK0mXOWr8fhA0PTp7aN6fB0g=" crossorigin="">
@endpush
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
const beneficiaryPosition = [{{ $request->latitude }}, {{ $request->longitude }}];
const beneficiaryMap = L.map('beneficiary-map').setView(beneficiaryPosition, 17);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom: 19, attribution: '&copy; OpenStreetMap'}).addTo(beneficiaryMap);
L.marker(beneficiaryPosition).addTo(beneficiaryMap);
</script>
@endpush
