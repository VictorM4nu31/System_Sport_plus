@if ($errors->any())
    <div {{ $attributes }}>
        <div class="message-error">{{ __('Whoops! Something went wrong.') }}</div>

        <ul class="message-error">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
