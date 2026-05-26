@extends('layouts.app')
@section('content')
<h1>استلامات المساهمات المسندة</h1>
<table class="table">
    <thead><tr><th>الرمز</th><th>المساهم</th><th>الهاتف</th><th>نوع المساهمة</th><th>الحالة</th><th></th></tr></thead>
    <tbody>
    @foreach($donations as $donation)
        <tr>
            <td>{{ $donation->code }}</td>
            <td>{{ $donation->donor_name ?? 'فاعل خير' }}</td>
            <td>{{ $donation->donor_phone }}</td>
            <td><x-donation-type :type="$donation->donation_type" /></td>
            <td><x-status-badge :status="$donation->status" /></td>
            <td><a href="{{ route('agent.pickups.show',$donation) }}">فتح</a></td>
        </tr>
    @endforeach
    </tbody>
</table>
{{ $donations->links() }}
@endsection
