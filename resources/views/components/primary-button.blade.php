<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-carbon border border-transparent rounded-md font-semibold text-body-sm text-white uppercase tracking-widest hover:bg-gray-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2']) }}>
    {{ $slot }}
</button>
