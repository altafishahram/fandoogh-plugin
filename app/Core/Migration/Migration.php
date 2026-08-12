<?php

declare(strict_types=1);

namespace Fandoogh\Core\Migration;

defined('ABSPATH') || exit;

interface Migration
{
    public function version(): string;

    public function up(): void;
}
