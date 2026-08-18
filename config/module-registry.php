<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

return [
    'description' => ['title' => __('توضیحات', 'fandoogh'), 'description' => __('مدیریت توضیحات دسته‌بندی محصولات.', 'fandoogh'), 'icon' => 'dashicons-edit-page', 'version' => '1.0.0'],
    'video' => ['title' => __('ویدیو', 'fandoogh'), 'description' => __('مدیریت ویدیو، پوستر و گالری دسته‌بندی محصولات.', 'fandoogh'), 'icon' => 'dashicons-video-alt3', 'version' => '1.0.0'],
    'faq' => ['title' => __('سوالات متداول', 'fandoogh'), 'description' => __('مدیریت سوالات و پاسخ‌های دسته‌بندی محصولات.', 'fandoogh'), 'icon' => 'dashicons-editor-help', 'version' => '1.0.0'],
    'product_faq' => ['title' => __('سؤالات متداول محصول', 'fandoogh'), 'description' => __('افزودن FAQ اختصاصی، شورت‌کد، Dynamic Tag و FAQ Schema برای هر محصول.', 'fandoogh'), 'icon' => 'dashicons-editor-help', 'version' => '1.0.0'],
    'product_reason' => ['title' => __('پاسخ تک‌سؤالی محصول', 'fandoogh'), 'description' => __('افزودن سؤال و پاسخ شاخص محصول همراه با شورت‌کد، Dynamic Tag و Product Schema.', 'fandoogh'), 'icon' => 'dashicons-lightbulb', 'version' => '1.0.0'],
    'reviews' => ['title' => __('نظرات', 'fandoogh'), 'description' => __('سیستم مدیریت نظرات محصولات.', 'fandoogh'), 'icon' => 'dashicons-star-filled', 'version' => '1.0.0'],
    'customers' => ['title' => __('مشتریان', 'fandoogh'), 'description' => __('مدیریت اطلاعات مشتریان.', 'fandoogh'), 'icon' => 'dashicons-groups', 'version' => '1.0.0'],
    'projects' => ['title' => __('پروژه‌ها', 'fandoogh'), 'description' => __('مدیریت پروژه‌های انجام‌شده.', 'fandoogh'), 'icon' => 'dashicons-portfolio', 'version' => '1.0.0'],
    'order-center' => [
        'title' => __('داشبورد مرکز سفارشات', 'fandoogh'),
        'description' => __('مدیریت حرفه‌ای سفارش‌های ووکامرس، وضعیت سفارش، مشتری، محصولات، پرداخت، ارسال، آمار و اطلاعات تکمیلی سفارش.', 'fandoogh'),
        'icon' => 'dashicons-cart',
        'version' => '1.0.0',
        'dependency' => 'woocommerce',
    ],
];
