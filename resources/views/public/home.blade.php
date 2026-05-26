@extends('layouts.app')
@section('content')
<section class="hero">
    <div>
        <h1>منصة محلية تحفظ أمانة هدية عيد الأضحى</h1>
        <p class="lead">تنظيم طلبات هدية العيد والمساهمات داخل مركز واحد، مع ستر كامل للبيانات وفصل تام بين المتبرعين ومستحقي هدية العيد.</p>
        <div class="actions">
            <a class="btn" href="{{ route('public.request.create') }}">طلب هدية العيد</a>
            <a class="btn gold" href="{{ route('public.donation.create') }}">المساهمة في الهدية</a>
        </div>
    </div>

    <div class="panel giving-animation" aria-label="توضيح لمسار المساهمة حتى تصل هدية العيد">
        <div class="giving-orbit" aria-hidden="true"></div>
        <div class="giving-path" aria-hidden="true"></div>
        <div class="giving-dot" aria-hidden="true"></div>
        <div class="giving-icon donor" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none">
                <path d="M20 12v8H4v-8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <path d="M2 7h20v5H2zM12 7v13M7.5 7C5 7 5 3.5 7.8 4.2 9.5 4.6 12 7 12 7s2.5-2.4 4.2-2.8C19 3.5 19 7 16.5 7" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
            </svg>
        </div>
        <div class="giving-icon team" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none">
                <path d="M8 11a4 4 0 1 0 8 0 4 4 0 0 0-8 0Z" stroke="currentColor" stroke-width="2"/>
                <path d="M4 21a8 8 0 0 1 16 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <path d="M12 7v8l3-2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <div class="giving-icon family" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none">
                <path d="M12 21s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 11c0 5.6-7 10-7 10Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                <path d="M9 13h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>
        <p class="giving-caption">المساهمة تصل عبر فريق التوزيع بستـر وخصوصية، دون كشف بيانات أي طرف للآخر.</p>
    </div>
</section>

<section class="panel">
    <div class="grid grid-3">
        <div class="stat"><strong>{{ $areasCount }}</strong><span>مناطق</span></div>
        <div class="stat"><strong>{{ $donationsCount }}</strong><span>مساهمات</span></div>
        <div class="stat"><strong>{{ $deliveredCount }}</strong><span>هدايا مسلمة</span></div>
    </div>
    <p class="privacy">لا يظهر اسم المتبرع للمستفيد، ولا تظهر بيانات المستفيد للمتبرع. بيانات الموقع لا يطلع عليها إلا الإدارة وفريق التوزيع المعتمد عند الإسناد.</p>
</section>
@endsection
