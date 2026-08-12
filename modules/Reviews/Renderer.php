<?php

declare(strict_types=1);

namespace Fandoogh\Modules\Reviews;

defined('ABSPATH') || exit;

final class Renderer
{
    public static function render(int $termId, array $args = []): string
    {
        $args = wp_parse_args($args, [
            'summary' => true,
            'list' => true,
            'form' => true,
        ]);

        $reviews = Reviews::get($termId);
        $count = Reviews::count($termId);
        $average = Reviews::average($termId);

        ob_start();
        require __DIR__ . '/Views/list.php';

        return (string) ob_get_clean();
    }
}
