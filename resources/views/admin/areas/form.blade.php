<div class="field">
    <label for="area-name">اسم المنطقة</label>
    <input id="area-name" name="name" value="{{ old('name', $area->name ?? '') }}" required>
</div>

<label>
    <input style="width:auto" type="checkbox" name="active" value="1" @checked(old('active', $area->active ?? true))>
    نشطة
</label>
