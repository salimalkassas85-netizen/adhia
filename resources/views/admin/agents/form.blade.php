<div class="grid grid-2">
    <div class="field"><label>الاسم</label><input name="name" value="{{ old('name', $agent->name ?? '') }}" required></div>
    <div class="field"><label>البريد</label><input name="email" type="email" value="{{ old('email', $agent->email ?? '') }}" required></div>
    <div class="field"><label>كلمة المرور</label><input name="password" type="password" @if(!isset($agent)) required @endif><p class="hint">اتركها فارغة عند التعديل للاحتفاظ بها.</p></div>
    <div class="field"><label>المنطقة</label><select name="area_id"><option value="">بدون منطقة ثابتة</option>@foreach($areas as $area)<option value="{{ $area->id }}" @selected(old('area_id', $agent->area_id ?? null) == $area->id)>{{ $area->name }}</option>@endforeach</select></div>
</div>
