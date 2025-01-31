@props([
    'label',
    'name',
    'value' => null,
    'type' => 'text', // Default type is text
    'id' => null, // Optional ID, defaults to name if not provided
    'required' => false,
    'step' => null,
    'min' => null,
    'max' => null,
])

<div>
    <label for="{{ $id ?? $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        {{ $label }}
    </label>
    <input type="{{ $type }}" name="{{ $name }}" id="{{ $id ?? $name }}"
        @if ($step) step="{{ $step }}" @endif
        @if ($required) required @endif
        @if ($value) value="{{ old($name, $value) }}" @endif
        @if ($min) min="{{ $min }}" @endif
        @if ($max) max="{{ $max }}" @endif
        class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white dark:bg-gray-700 text-gray-700 dark:text-white
            @error($name) border-red-500 dark:border-red-400 @enderror">

    @error($name)
        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>
