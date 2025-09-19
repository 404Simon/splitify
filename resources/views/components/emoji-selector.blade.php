@props([
    'id' => 'emoji-selector-' . uniqid(),
])

<div x-data="{
    isOpen: false,
    selectedEmoji: '{{ $value }}',
    searchQuery: '',
    activeCategory: 'all',
    emojiData: {{ json_encode($emojiData) }},
    categories: {{ json_encode($categories) }},
    filteredResults: [],
    // Map of category IDs to their original keys for faster lookup
    categoryMap: {},

    init() {
        // Initialize the category map for faster lookups
        for (const category in this.emojiData) {
            const formattedCat = category.toLowerCase().replace(/-/g, '_');
            this.categoryMap[formattedCat] = category;
        }

        // Use $nextTick to ensure DOM is ready
        this.$nextTick(() => {
            this.$watch('selectedEmoji', (value) => {
                this.$refs.hiddenInput.value = value;
            });

            // Debounce search input for better performance
            let debounceTimeout;
            this.$watch('searchQuery', () => {
                clearTimeout(debounceTimeout);
                debounceTimeout = setTimeout(() => {
                    this.updateFilteredResults();
                }, 200);
            });

            this.$watch('activeCategory', () => {
                this.updateFilteredResults();
            });

            this.updateFilteredResults();
        });
    },

    positionDropdown() {
        this.$nextTick(() => {
            const dropdown = this.$refs.dropdown;
            const button = this.$refs.button;
            if (!dropdown || !button) return;

            const buttonRect = button.getBoundingClientRect();
            const dropdownHeight = 350; // Approximate height of dropdown
            const viewportHeight = window.innerHeight;
            const spaceBelow = viewportHeight - buttonRect.bottom;
            const spaceAbove = buttonRect.top;

            // Position dropdown above if there's not enough space below
            if (spaceBelow < dropdownHeight && spaceAbove > spaceBelow) {
                dropdown.style.bottom = '100%';
                dropdown.style.top = 'auto';
                dropdown.style.marginBottom = '8px';
                dropdown.style.marginTop = '0px';
            } else {
                dropdown.style.top = '100%';
                dropdown.style.bottom = 'auto';
                dropdown.style.marginTop = '8px';
                dropdown.style.marginBottom = '0px';
            }
        });
    },

    updateFilteredResults() {
        let result = [];
        const searchQueryLower = this.searchQuery.toLowerCase();

        // Get emojis based on active category
        if (this.activeCategory === 'all') {
            // More efficient way to flatten arrays
            result = Object.values(this.emojiData).flat();
        } else {
            // Use the pre-built mapping for faster lookup
            const categoryKey = this.categoryMap[this.activeCategory];
            if (this.emojiData[categoryKey]) {
                result = this.emojiData[categoryKey];
            }
        }

        // Filter results by search query if present
        if (searchQueryLower) {
            this.filteredResults = result.filter(item =>
                (item.emoji && item.emoji.toLowerCase().includes(searchQueryLower)) ||
                (item.title && item.title.toLowerCase().includes(searchQueryLower))
            );
        } else {
            this.filteredResults = result;
        }
    },

    selectEmoji(emoji) {
        this.selectedEmoji = emoji;
        this.isOpen = false;
    },

    setCategory(category) {
        this.activeCategory = category;
    }
}" class="relative">
    <input type="hidden" name="{{ $name }}" x-ref="hiddenInput" value="{{ $value }}">

    <button type="button" @click="isOpen = true; positionDropdown()" x-ref="button"
        class="flex items-center justify-center w-12 h-12 text-2xl bg-white dark:bg-gray-700 border dark:border-gray-600 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400">
        <span x-text="selectedEmoji"></span>
    </button>

    <div x-show="isOpen" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95" @click.away="isOpen = false" x-ref="dropdown"
        class="absolute z-50 bg-white dark:bg-gray-800 rounded-lg shadow-xl border dark:border-gray-700"
        style="width: 300px;">
        <div class="p-3 border-b dark:border-gray-700">
            <div class="flex items-center">
                <div class="flex-1">
                    <input type="text"
                        class="w-full px-3 py-2 border dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        placeholder="Search emoji..." x-model="searchQuery">
                </div>
            </div>
        </div>

        <div class="p-2 border-b dark:border-gray-700 overflow-x-auto whitespace-nowrap">
            <div class="flex justify-around space-x-1">
                <template x-for="category in categories" :key="category.id">
                    <button type="button" @click.prevent="setCategory(category.id)"
                        :class="{
                            'w-9 h-9 flex items-center justify-center text-xl rounded-md focus:outline-none transition-all': true,
                            'bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-300 scale-110 shadow-sm': activeCategory ===
                                category.id,
                            'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700': activeCategory !==
                                category.id
                        }"
                        :title="category.name" x-text="category.emoji"></button>
                </template>
            </div>
        </div>

        <div class="p-2 h-48 overflow-y-auto">
            <div class="grid grid-cols-7 gap-1">
                <template x-for="(item, index) in filteredResults" :key="index">
                    <button type="button" @click.prevent="selectEmoji(item.emoji)"
                        class="flex items-center justify-center w-9 h-9 text-lg hover:bg-gray-100 dark:hover:bg-gray-700 rounded focus:outline-none transition-transform hover:scale-110"
                        :title="item.title" x-text="item.emoji"></button>
                </template>
            </div>
            <div x-show="filteredResults.length === 0" class="py-8 text-center text-gray-500 dark:text-gray-400">
                No emojis found
            </div>
        </div>
    </div>
</div>
