<div class="form-group">
    <input type="text" name="name" class="form-control" value="{{ old("name") ?? $product->name ?? "" }}" placeholder="Имя на англ.">
</div>
<div class="form-group">
    <input type="text" name="name_RU" class="form-control" value="{{ old("name_RU") ?? $product->name_RU ?? "" }}" placeholder="Имя на рус.">
</div>
<div class="form-group">
    <input type="text" name="name_BG" class="form-control" value="{{ old("name_BG") ?? $product->name_BG ?? "" }}" placeholder="Имя на болг.">
</div>
<div class="form-group">
    <input type="text" name="name_DE" class="form-control" value="{{ old("name_DE") ?? $product->name_DE ?? "" }}" placeholder="Имя на нем.">
</div>
<div class="form-group">
    <textarea name="description" rows="4" placeholder="Описание на англ." class="form-control">{{ old("description") ?? $product->description ?? "" }}</textarea>
</div>
<div class="form-group">
    <textarea name="description_RU" rows="4" placeholder="Описание на рус." class="form-control">{{ old("description_RU") ?? $product->description_RU ?? "" }}</textarea>
</div>
<div class="form-group">
    <textarea name="description_BG" rows="4" placeholder="Описание на болг." class="form-control">{{ old("description_BG") ?? $product->description_BG ?? "" }}</textarea>
</div>
<div class="form-group">
    <textarea name="description_DE" rows="4" placeholder="Описание на нем." class="form-control">{{ old("description_DE") ?? $product->description_DE ?? "" }}</textarea>
</div>
<div class="form-group">
    <input type="text" name="price" class="form-control" value="{{ old("price") ?? $product->price ?? "" }}" placeholder="Цена продукта">
</div>
<div class="form-group">
    <input type="file" placeholder="Выберите фото продукта" name="photo">
</div>
