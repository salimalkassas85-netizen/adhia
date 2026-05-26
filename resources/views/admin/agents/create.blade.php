@extends('layouts.app')
@section('content')
<div class="panel"><h1>إضافة عضو توزيع</h1><form method="post" action="{{ route('admin.agents.store') }}">@csrf@include('admin.agents.form')<button>حفظ الحساب</button></form></div>
@endsection
