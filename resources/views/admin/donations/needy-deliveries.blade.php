@extends('layouts.app')
@section('content')
<div class="actions" style="justify-content:space-between">
    <div>
        <h1>تسليمات المحتاجين المجمعة</h1>
        <p class="privacy">اعرض هنا فقط المساهمات التي تم استلامها من المتبرعين ولم تُسلم للمحتاج بعد. يتم تسليم كل الجاهز للمحتاج مرة واحدة.</p>
    </div>
    <div class="actions">
        <a class="btn secondary" href="{{ route('admin.donations.index') }}">طلبات التوصيل</a>
        <a class="btn secondary" href="{{ route('admin.dashboard') }}">لوحة الإدارة</a>
    </div>
</div>

@if(session('status'))
    <div class="notice" style="margin-bottom:14px">{{ session('status') }}</div>
@endif

<div class="panel">
    <p class="notice">
        الإجماليات هنا لا تشمل التبرعات المنتظرة عند المتبرعين؛ لأنها لم تصل لأدمن المنطقة بعد. عند الضغط على "تسليم كل الجاهز" يتم تحويل كل مساهمات هذا المحتاج ذات حالة "تم الاستلام" إلى "تم التسليم" دفعة واحدة.
    </p>
</div>

<div class="table-responsive" style="margin-top:18px">
<table class="table">
    <thead>
        <tr>
            <th>المحتاج</th>
            <th>المنطقة</th>
            <th>العنوان</th>
            <th>إجمالي الفلوس الجاهزة</th>
            <th>إجمالي اللحم الجاهز</th>
            <th>عدد المساهمات</th>
            <th>موقع التسليم</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    @forelse($needyDeliveries as $beneficiary)
        <tr>
            <td>{{ $beneficiary->first_name }}<br><span class="privacy">{{ $beneficiary->code }}</span></td>
            <td>{{ $beneficiary->area?->name ?? 'غير محددة' }}</td>
            <td>{{ $beneficiary->full_address }} @if($beneficiary->landmark)<br><span class="privacy">{{ $beneficiary->landmark }}</span>@endif</td>
            <td>{{ number_format((float) ($beneficiary->ready_money_total ?? 0), 2) }} جنيه</td>
            <td>{{ number_format((float) ($beneficiary->ready_meat_kg_total ?? 0), 2) }} كجم</td>
            <td>{{ $beneficiary->ready_donations_count }}</td>
            <td>
                @if($beneficiary->latitude && $beneficiary->longitude)
                    <a target="_blank" rel="noopener" href="{{ $beneficiary->mapsUrl() }}">فتح</a>
                @else
                    -
                @endif
            </td>
            <td>
                <form method="post" action="{{ route('admin.donations.needy-deliveries.deliver', $beneficiary) }}" onsubmit="return confirm('تأكيد تسليم كل المساهمات الجاهزة لهذا المحتاج؟');">
                    @csrf
                    <button>تسليم كل الجاهز</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="8">لا توجد مساهمات جاهزة للتسليم للمحتاجين الآن.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
{{ $needyDeliveries->links() }}
@endsection
