@props(['label', 'name', 'value' => 1, 'id' => null, 'required' => false, 'checked' => false])

<div>
    <div class="flex items-start">
        <div class="flex items-center h-6">
            <input id="{{ $id ?? $name }}" name="{{ $name }}" type="checkbox" value="{{ $value }}"
                @if ($required) required @endif @if (old($name, $checked)) checked @endif
                class="focus:ring-indigo-500 h-5 w-5 text-indigo-600 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-700 dark:focus:ring-indigo-600 cursor-pointer
                    @error($name) border-red-500 dark:border-red-400 @enderror">
        </div>
        <div class="ml-3 text-base">
            <label for="{{ $id ?? $name }}" class="font-medium text-gray-700 dark:text-gray-300 cursor-pointer select-none">
                {{ $label }}
            </label>
        </div>
    </div>

    @error($name)
        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>
