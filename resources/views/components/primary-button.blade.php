<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-body-sm text-white uppercase tracking-widest']) }}>
    {{ $slot }}
</button>
