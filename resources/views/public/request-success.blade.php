@extends('layouts.app')
@section('content')
<div class="panel">
    <h1>تم استلام طلب هدية العيد</h1>
    <p>جزاك الله خيرًا على الثقة. سيُراجع فريق المبادرة الطلب بسرية وستر.</p>
    <p>رمز المتابعة: <strong>{{ $code }}</strong></p>
    <p class="privacy">لا تعرض هذه الصفحة العنوان أو الموقع حفاظًا على الخصوصية.</p>
</div>
@endsection
