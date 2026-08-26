<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-error border border-transparent rounded-md font-semibold text-body-sm text-white uppercase tracking-widest hover:bg-error-dark active:bg-error-700 focus:outline-none focus:ring-2 focus:ring-error focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 btn-accessible']) }}>
    {{ $slot }}
</button>
