<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;

class EmojiSelector extends Component
{
    public function __construct(
        public string $defaultEmoji = '😀',
        public string $name = 'emoji',
        public ?string $value = null,
    ) {
        $this->value = $value ?? $defaultEmoji;
    }

    public function render(): View|Closure|string
    {
        $emojiData = Cache::remember('emoji_data', 60 * 60 * 24, function () {
            return json_decode(file_get_contents(public_path('emoji.json')), true);
        });

        $categoryEmojis = [
            'all' => '🔍',
            'activity' => '⚽',
            'flags' => '🏳️',
            'food-drink' => '🍔',
            'nature' => '🌿',
            'objects' => '💎',
            'people' => '😀',
            'symbols' => '❤️',
            'travel-places' => '🚗',
        ];

        $categories = collect([
            ['id' => 'all', 'name' => 'All', 'emoji' => $categoryEmojis['all']],
        ]);

        $categories = $categories->merge(
            collect($emojiData)->keys()->map(function ($category) use ($categoryEmojis) {
                $categoryId = strtolower(str_replace('-', '_', $category));
                $categoryName = str_replace('-', ' & ', $category);
                $emoji = $categoryEmojis[strtolower($category)] ?? '📋';

                return [
                    'id' => $categoryId,
                    'name' => $categoryName,
                    'emoji' => $emoji,
                ];
            })
        );

        return view('components.emoji-selector', [
            'emojiData' => $emojiData,
            'categories' => $categories->all(),
        ]);
    }
}
