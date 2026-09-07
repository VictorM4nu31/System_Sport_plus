@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-carbon dark:focus:border-gray-400 focus:ring-carbon dark:focus:ring-gray-400 rounded-md shadow-sm']) !!}>
