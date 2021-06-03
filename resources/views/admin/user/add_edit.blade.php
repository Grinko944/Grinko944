<div class="form-group row">
    <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Имя') }}</label>

    <div class="col-md-6">
        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name"
               value="{{ old('name') ?? $user->name ?? "" }}" required autocomplete="name" autofocus>

        @error('name')
        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
        @enderror
    </div>
</div>

<div class="form-group row">
    <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('email') }}</label>

    <div class="col-md-6">
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email"
               value="{{ old('email') ?? $user->email ?? "" }}" required autocomplete="email">

        @error('email')
        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
        @enderror
    </div>
</div>

<div class="form-group row">
    <label for="role" class="col-md-4 col-form-label text-md-right">{{ __('Роль') }}</label>

    <div class="col-md-6">
        <input id="role" type="number" class="form-control @error('email') is-invalid @enderror" name="role"
               value="{{ old('role') ?? $user->role_id ?? "" }}" required>

        <div class="values" style="margin: 5px 0">
            <button type="button" class="btn btn-info btn_value_edit_order" title="Администратор">1</button>
            <button type="button" class="btn btn-info btn_value_edit_order" title="шеф-повар">2</button>
        </div>

        <script>
            let btns = document.querySelectorAll(".btn_value_edit_order"),
                editInput = document.querySelector("#role")

            btns.forEach(btn => {
                btn.addEventListener("click", () => {
                    editInput.value = ""
                    editInput.value = btn.textContent
                })
            })
        </script>
    </div>
</div>

<div class="form-group row">
    <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Пароль') }}</label>

    <div class="col-md-6">
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
               name="password" required autocomplete="new-password">

        @error('password')
        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
        @enderror
    </div>
</div>
