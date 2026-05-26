@extends('layouts.app')
@php
    $activeDonations = $donations->where('status', '!=', 'cancelled');
    $pendingDonations = $activeDonations->where('status', 'pending');
    $receivedDonations = $activeDonations->where('status', 'received');
    $deliveredDonations = $activeDonations->where('status', 'completed');
    $moneyTotal = $activeDonations->sum(fn ($donation) => (float) ($donation->amount ?? 0));
    $meatTotal = $activeDonations->sum(fn ($donation) => (float) ($donation->meat_kg ?? 0));
    $deliveredMoneyTotal = $deliveredDonations->sum(fn ($donation) => (float) ($donation->amount ?? 0));
    $deliveredMeatTotal = $deliveredDonations->sum(fn ($donation) => (float) ($donation->meat_kg ?? 0));
@endphp
@section('content')
<div class="panel">
    <h1>متابعة طلب {{ $giftRequest->code }}</h1>
    <p class="notice">هذه الصفحة تعرض للمحتاج إجمالي ما خُصص له وحالة كل تبرع على حدة، بدون عرض أسماء أو أرقام المتبرعين.</p>
    <div class="grid grid-3">
        <div class="stat"><strong>{{ $activeDonations->count() }}</strong><span>تبرعات مرتبطة</span></div>
        <div class="stat"><strong>{{ number_format($moneyTotal, 2) }}</strong><span>جنيه مخصص</span></div>
        <div class="stat"><strong>{{ number_format($meatTotal, 2) }}</strong><span>كجم لحم مخصص</span></div>
    </div>
    <hr>
    <p><strong>حالة الطلب العامة:</strong> <x-status-badge :status="$giftRequest->status" /></p>
    <p><strong>المنطقة:</strong> {{ $giftRequest->area?->name ?? 'غير محددة' }}</p>
    <p><strong>في انتظار الاستلام من المتبرعين:</strong> {{ $pendingDonations->count() }}</p>
    <p><strong>تم استلامها من المتبرعين وجاهزة للتسليم:</strong> {{ $receivedDonations->count() }}</p>
    <p><strong>تم تسليمها لك:</strong> {{ $deliveredDonations->count() }}</p>
    <p><strong>إجمالي ما تم تسليمه فعليًا:</strong> {{ number_format($deliveredMoneyTotal, 2) }} جنيه / {{ number_format($deliveredMeatTotal, 2) }} كجم</p>
</div>

<div class="panel" style="margin-top:18px">
    <h2>حالة كل تبرع</h2>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>نوع التبرع</th>
                    <th>المبلغ</th>
                    <th>اللحم بالكيلو</th>
                    <th>الحالة</th>
                    <th>تاريخ التسجيل</th>
                </tr>
            </thead>
            <tbody>
                @forelse($activeDonations as $donation)
                    <tr>
                        <td>تبرع {{ $loop->iteration }}</td>
                        <td>{{ \App\Support\ArabicLabels::donationType($donation->donation_type) }}</td>
                        <td>{{ $donation->amount ? number_format((float) $donation->amount, 2).' جنيه' : '-' }}</td>
                        <td>{{ $donation->meat_kg ? number_format((float) $donation->meat_kg, 2).' كجم' : '-' }}</td>
                        <td><x-status-badge :status="$donation->status" /></td>
                        <td>{{ $donation->created_at?->format('Y-m-d') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">لا توجد تبرعات مرتبطة بهذا الطلب حتى الآن.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <p class="privacy" style="margin-top:12px">حفاظًا على الستر، لا تظهر هنا بيانات المتبرعين أو أرقامهم أو عناوينهم.</p>
</div>

<div class="actions">
    <a class="btn secondary" href="{{ route('public.request.status.form') }}">متابعة طلب آخر</a>
    <a class="btn" href="{{ route('home') }}">العودة للرئيسية</a>
</div>
@endsection
