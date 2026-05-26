@extends('layouts.app')
@section('content')
<div class="panel">
    <h1>تم استلام طلب هدية العيد</h1>
    <p>جزاك الله خيرًا على الثقة. سيُراجع فريق المبادرة الطلب بسرية وستر.</p>
    <p>رمز المتابعة: <strong>{{ $code }}</strong></p>
    <p>الحالة الحالية: <x-status-badge :status="$status" /></p>
    @if($linkedDonation)
        <p class="notice">تم ربط طلبك بمساهمة، وأدمن المنطقة مسؤول عن الاستلام من المتبرع والتسليم لك. حالة الطلب واحدة مشتركة.</p>
    @else
        <p class="notice">بانتظار ربط الطلب بمساهمة مناسبة داخل منطقتك.</p>
    @endif
    <div class="actions">
        <a class="btn" href="{{ route('public.request.status.show', $code) }}">متابعة التبرعات المرتبطة بالطلب</a>
        <a class="btn secondary" href="{{ route('home') }}">العودة للرئيسية</a>
    </div>
    <p class="privacy">لا تعرض هذه الصفحة العنوان أو الموقع حفاظًا على الخصوصية.</p>
</div>
@endsection
