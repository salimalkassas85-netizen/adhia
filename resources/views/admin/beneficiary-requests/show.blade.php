@extends('layouts.app')
@php
    $linkedDonations = $request->allocations
        ->pluck('donation')
        ->filter()
        ->sortByDesc('created_at')
        ->values();

    $activeDonations = $linkedDonations->where('status', '!=', 'cancelled');
    $readyDonations = $activeDonations->where('status', 'received');
    $deliveredDonations = $activeDonations->where('status', 'completed');
    $pendingDonations = $activeDonations->where('status', 'pending');

    $moneyTotal = $activeDonations->sum(fn ($donation) => (float) ($donation->amount ?? 0));
    $meatTotal = $activeDonations->sum(fn ($donation) => (float) ($donation->meat_kg ?? 0));
    $readyMoneyTotal = $readyDonations->sum(fn ($donation) => (float) ($donation->amount ?? 0));
    $readyMeatTotal = $readyDonations->sum(fn ($donation) => (float) ($donation->meat_kg ?? 0));

    $deliveryStatus = $activeDonations->isNotEmpty() && $activeDonations->every(fn ($donation) => $donation->status === 'completed')
        ? 'completed'
        : ($readyDonations->isNotEmpty() ? 'received' : 'pending');
@endphp
@section('content')
<div class="grid grid-2">
    <div class="panel">
        <h1>طلب {{ $request->code }}</h1>
        <p><strong>الاسم الأول:</strong> {{ $request->first_name }}</p>
        <p><strong>الهاتف:</strong> <a href="tel:{{ $request->phone }}">{{ $request->phone }}</a></p>
        <p><strong>المنطقة:</strong> {{ $request->area?->name }}</p>
        <p><strong>حالة المحتاج:</strong> <x-status-badge :status="$deliveryStatus" /></p>
        <p><strong>عدد أفراد الأسرة:</strong> {{ $request->family_members_count ?? 'غير محدد' }}</p>
        <p><strong>أطفال:</strong> {{ $request->has_children ? 'نعم' : 'لا' }} | <strong>كبار سن:</strong> {{ $request->has_elderly ? 'نعم' : 'لا' }}</p>
        <p><strong>العنوان:</strong> {{ $request->full_address }}</p>
        <p><strong>علامة قريبة:</strong> {{ $request->landmark ?? 'غير مذكورة' }}</p>
        <a class="btn secondary" target="_blank" rel="noopener" href="{{ $request->mapsUrl() }}">فتح الموقع في OpenStreetMap</a>
        <div id="beneficiary-map" class="map" style="margin-top:14px"></div>
    </div>

    <div class="panel">
        <h2>ملخص التبرعات المرتبطة بالمحتاج</h2>
        <p class="notice">كل متبرع يظهر كطلب استلام منفصل. بعد استلام كل التبرعات الجاهزة، اضغط "تسليم كل الجاهز للمحتاج" لتسليمها مرة واحدة.</p>
        <p><strong>عدد التبرعات:</strong> {{ $activeDonations->count() }}</p>
        <p><strong>في انتظار الاستلام:</strong> {{ $pendingDonations->count() }}</p>
        <p><strong>تم استلامها وجاهزة للتسليم:</strong> {{ $readyDonations->count() }}</p>
        <p><strong>تم تسليمها:</strong> {{ $deliveredDonations->count() }}</p>
        <p><strong>إجمالي المال المخصص:</strong> {{ number_format($moneyTotal, 2) }} جنيه</p>
        <p><strong>إجمالي اللحم المخصص:</strong> {{ number_format($meatTotal, 2) }} كجم</p>
        <hr>
        <p><strong>الجاهز للتسليم الآن:</strong> {{ number_format($readyMoneyTotal, 2) }} جنيه / {{ number_format($readyMeatTotal, 2) }} كجم</p>
        @if($readyDonations->isNotEmpty())
            <form method="post" action="{{ route('admin.donations.needy-deliveries.deliver', $request) }}" onsubmit="return confirm('تأكيد تسليم كل التبرعات التي تم استلامها لهذا المحتاج؟');">
                @csrf
                <button>تسليم كل الجاهز للمحتاج</button>
            </form>
        @endif
    </div>
</div>

<div class="panel" style="margin-top:18px">
    <h2>المتبرعون لهذا المحتاج</h2>
    <p class="privacy">هذه البيانات تظهر لأدمن المنطقة فقط لأنه المسؤول عن الاستلام من المتبرعين والتسليم للمحتاج.</p>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>كود المساهمة</th>
                    <th>المتبرع</th>
                    <th>الهاتف</th>
                    <th>نوع/قيمة التبرع</th>
                    <th>عنوان الاستلام</th>
                    <th>الحالة</th>
                    <th>إجراء</th>
                </tr>
            </thead>
            <tbody>
            @forelse($linkedDonations as $donation)
                <tr>
                    <td><a href="{{ route('admin.donations.show', $donation) }}">{{ $donation->code }}</a></td>
                    <td>{{ $donation->donor_name ?? 'فاعل خير' }}</td>
                    <td><a href="tel:{{ $donation->donor_phone }}">{{ $donation->donor_phone }}</a></td>
                    <td>
                        {{ \App\Support\ArabicLabels::donationType($donation->donation_type) }}
                        @if($donation->amount)
                            <br>{{ number_format((float) $donation->amount, 2) }} جنيه
                        @endif
                        @if($donation->meat_kg)
                            <br>{{ number_format((float) $donation->meat_kg, 2) }} كجم
                        @endif
                    </td>
                    <td>
                        {{ $donation->pickup_address }}
                        @if($donation->pickupMapsUrl())
                            <br><a target="_blank" rel="noopener" href="{{ $donation->pickupMapsUrl() }}">فتح موقع الاستلام</a>
                        @endif
                    </td>
                    <td><x-status-badge :status="$donation->status" /></td>
                    <td>
                        @if($donation->status === 'pending')
                            <form method="post" action="{{ route('admin.donations.receive', $donation) }}" onsubmit="return confirm('تأكيد استلام هذه المساهمة من المتبرع؟');">
                                @csrf
                                <button>تم الاستلام</button>
                            </form>
                        @elseif($donation->status === 'received')
                            <span class="badge ok">جاهز للتسليم للمحتاج</span>
                        @elseif($donation->status === 'completed')
                            <span class="badge ok">تم التسليم للمحتاج</span>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">لا توجد تبرعات مرتبطة بهذا المحتاج حتى الآن.</td></tr>
            @endforelse
            </tbody>
        </table>
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
