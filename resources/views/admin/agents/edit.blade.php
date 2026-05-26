@extends('layouts.app')
@section('content')
<div class="panel"><h1>تعديل عضو توزيع</h1><form method="post" action="{{ route('admin.agents.update',$agent) }}">@csrf @method('put')@include('admin.agents.form')<button>تحديث</button></form></div>
@endsection
