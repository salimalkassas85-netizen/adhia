<div class="grid grid-2">
    <div class="field"><label>الاسم</label><input name="name" value="{{ old('name', $adminUser->name ?? '') }}" required></div>
    <div class="field"><label>البريد الإلكتروني</label><input name="email" type="email" value="{{ old('email', $adminUser->email ?? '') }}" required></div>
    <div class="field">
        <label>كلمة المرور</label>
        <input name="password" type="password" @if(!isset($adminUser)) required @endif>
        <p class="hint">اتركها فارغة عند التعديل للاحتفاظ بكلمة المرور الحالية.</p>
    </div>
    <div class="field">
        <label>نطاق الإدارة</label>
        <select name="area_id">
            <option value="">أدمن عام لكل المناطق</option>
            @foreach($areas as $area)
                <option value="{{ $area->id }}" @selected(old('area_id', $adminUser->area_id ?? null) == $area->id)>{{ $area->name }}</option>
            @endforeach
        </select>
        <p class="hint">لو اخترت منطقة، الحساب سيشاهد طلبات ومساهمات هذه المنطقة فقط.</p>
    </div>
</div>
