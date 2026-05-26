@extends('layouts.app')
@section('content')
<h1>طلبات التوزيع المسندة</h1>
<table class="table"><thead><tr><th>الرمز</th><th>الاسم الأول</th><th>المنطقة</th><th>الحالة</th><th></th></tr></thead><tbody>
@foreach($requests as $request)
<tr><td>{{ $request->code }}</td><td>{{ $request->first_name }}</td><td>{{ $request->area?->name }}</td><td><x-status-badge :status="$request->status" /></td><td><a href="{{ route('agent.requests.show',$request) }}">فتح</a></td></tr>
@endforeach
</tbody></table>{{ $requests->links() }}
@endsection
