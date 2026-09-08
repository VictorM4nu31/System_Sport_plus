@if ($errors->any())
    <div {{ $attributes }} role="alert">
        <div class="message-error">{{ __('Revisa los siguientes errores.') }}</div>

        <ul class="message-error list-disc pl-5 mt-2 flex flex-col gap-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
