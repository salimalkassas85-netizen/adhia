@extends('layouts.app')
@section('content')
<div class="actions" style="justify-content:space-between">
    <h1>طلبات هدية العيد</h1>
    <div class="actions">
        <a class="btn secondary" href="{{ route('admin.dashboard') }}">لوحة الإدارة</a>
    </div>
</div>

<div class="panel">
    <p class="notice">عند ربط الطلب بمساهمة يصبح طلب توصيل واحد مسؤول عنه أدمن المنطقة مباشرة.</p>
    <div class="actions">
        <a class="btn {{ $selectedStatus ? 'secondary' : '' }}" href="{{ route('admin.beneficiary-requests.index') }}">الكل</a>
        @foreach(\App\Support\ArabicLabels::beneficiaryStatusOptions() as $status => $label)
            <a class="btn {{ $selectedStatus === $status ? '' : 'secondary' }}" href="{{ route('admin.beneficiary-requests.index', ['status' => $status]) }}">{{ $label }}</a>
        @endforeach
    </div>
</div>

<div class="table-responsive">
<table class="table">
    <thead><tr><th>الرمز</th><th>الاسم الأول</th><th>الهاتف</th><th>المنطقة</th><th>حالة المحتاج</th><th>عدد المتبرعين</th><th>جاهز للتسليم</th><th>الموقع</th><th></th></tr></thead>
    <tbody>
    @forelse($requests as $request)
        @php
            $linkedDonations = $request->allocations->pluck('donation')->filter();
            $activeDonations = $linkedDonations->where('status', '!=', 'cancelled');
            $readyDonations = $activeDonations->where('status', 'received');
            $deliveryStatus = $activeDonations->isNotEmpty() && $activeDonations->every(fn ($donation) => $donation->status === 'completed')
                ? 'completed'
                : ($readyDonations->isNotEmpty() ? 'received' : 'pending');
        @endphp
        <tr>
            <td>{{ $request->code }}</td>
            <td>{{ $request->first_name }}</td>
            <td><a href="tel:{{ $request->phone }}">{{ $request->phone }}</a></td>
            <td>{{ $request->area?->name }}</td>
            <td><x-status-badge :status="$deliveryStatus" /></td>
            <td>{{ $activeDonations->count() }}</td>
            <td>{{ $readyDonations->count() }}</td>
            <td><a target="_blank" rel="noopener" href="{{ $request->mapsUrl() }}">فتح</a></td>
            <td><a class="btn secondary" href="{{ route('admin.beneficiary-requests.show',$request) }}">عرض المتبرعين</a></td>
        </tr>
    @empty
        <tr><td colspan="9">لا توجد طلبات مطابقة.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
{{ $requests->links() }}
@endsection
