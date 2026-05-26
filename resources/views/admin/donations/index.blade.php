@extends('layouts.app')
@section('content')
<div class="actions" style="justify-content:space-between">
    <h1>طلبات التوصيل والمساهمات</h1>
    <a class="btn" href="{{ route('admin.donations.needy-deliveries') }}">تسليمات المحتاجين</a>
    <a class="btn secondary" href="{{ route('admin.dashboard') }}">لوحة الإدارة</a>
</div>

<div class="panel">
    <p class="notice">كل مساهمة مرتبطة بحالة تعتبر طلب استلام من المتبرع. بعد الاستلام تظهر في صفحة تسليمات المحتاجين لتسليم كل المخصصات الجاهزة لنفس المحتاج مرة واحدة.</p>
    <div class="actions">
        <a class="btn {{ $selectedStatus ? 'secondary' : '' }}" href="{{ route('admin.donations.index') }}">الكل</a>
        @foreach(\App\Support\ArabicLabels::donationStatusOptions() as $status => $label)
            <a class="btn {{ $selectedStatus === $status ? '' : 'secondary' }}" href="{{ route('admin.donations.index', ['status' => $status]) }}">{{ $label }}</a>
        @endforeach
    </div>
</div>

<div class="table-responsive">
<table class="table">
    <thead><tr><th>الرمز</th><th>المتبرع</th><th>الهاتف</th><th>المنطقة</th><th>المحتاج المرتبط</th><th>النوع</th><th>الحالة</th><th>أدمن المنطقة</th><th>موقع الاستلام</th><th></th></tr></thead>
    <tbody>
    @forelse($donations as $donation)
        @php $beneficiary = $donation->allocations->first()?->beneficiaryRequest; @endphp
        <tr>
            <td>{{ $donation->code }}</td>
            <td>{{ $donation->donor_name ?? 'فاعل خير' }}</td>
            <td><a href="tel:{{ $donation->donor_phone }}">{{ $donation->donor_phone }}</a></td>
            <td>{{ $donation->targetArea?->name ?? $donation->donorArea?->name ?? 'غير محددة' }}</td>
            <td>{{ $beneficiary?->first_name ? $beneficiary->first_name.' - '.$beneficiary->code : 'لم يحدد بعد' }}</td>
            <td><x-donation-type :type="$donation->donation_type" /></td>
            <td><x-status-badge :status="$donation->status" /></td>
            <td>{{ $donation->assignedAdmin?->name ?? 'لم يتم تحديده' }}</td>
            <td>@if($donation->pickupMapsUrl())<a target="_blank" rel="noopener" href="{{ $donation->pickupMapsUrl() }}">فتح</a>@else - @endif</td>
            <td><a class="btn secondary" href="{{ route('admin.donations.show',$donation) }}">إدارة</a></td>
        </tr>
    @empty
        <tr><td colspan="10">لا توجد مساهمات مطابقة.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
{{ $donations->links() }}
@endsection
