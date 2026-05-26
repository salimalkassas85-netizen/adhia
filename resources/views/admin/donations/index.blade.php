@extends('layouts.app')
@section('content')
<div class="actions" style="justify-content:space-between">
    <h1>المساهمات والمتبرعون</h1>
    <a class="btn secondary" href="{{ route('admin.dashboard') }}">لوحة الإدارة</a>
</div>

<div class="panel">
    <div class="actions">
        <a class="btn {{ $selectedStatus ? 'secondary' : '' }}" href="{{ route('admin.donations.index') }}">الكل</a>
        @foreach(['pending' => 'جديدة', 'confirmed' => 'مؤكدة', 'received' => 'تم الاستلام', 'allocated' => 'مخصصة', 'in_distribution' => 'قيد التوزيع', 'completed' => 'مكتملة'] as $status => $label)
            <a class="btn {{ $selectedStatus === $status ? '' : 'secondary' }}" href="{{ route('admin.donations.index', ['status' => $status]) }}">{{ $label }}</a>
        @endforeach
    </div>
</div>

<table class="table">
    <thead><tr><th>الرمز</th><th>المتبرع</th><th>الهاتف</th><th>منطقة المتبرع</th><th>منطقة التوزيع</th><th>النوع</th><th>الحالة</th><th>موقع الاستلام</th><th></th></tr></thead>
    <tbody>
    @forelse($donations as $donation)
        <tr>
            <td>{{ $donation->code }}</td>
            <td>{{ $donation->donor_name ?? 'فاعل خير' }}</td>
            <td><a href="tel:{{ $donation->donor_phone }}">{{ $donation->donor_phone }}</a></td>
            <td>{{ $donation->donorArea?->name ?? 'غير محددة' }}</td>
            <td>{{ $donation->targetArea?->name ?? 'تحتاج تخصيص' }}</td>
            <td><x-donation-type :type="$donation->donation_type" /></td>
            <td><x-status-badge :status="$donation->status" /></td>
            <td>@if($donation->pickupMapsUrl())<a target="_blank" rel="noopener" href="{{ $donation->pickupMapsUrl() }}">فتح</a>@else - @endif</td>
            <td><a class="btn secondary" href="{{ route('admin.donations.show',$donation) }}">إدارة</a></td>
        </tr>
    @empty
        <tr><td colspan="9">لا توجد مساهمات مطابقة.</td></tr>
    @endforelse
    </tbody>
</table>
{{ $donations->links() }}
@endsection
