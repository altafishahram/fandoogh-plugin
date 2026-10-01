<?php

declare(strict_types=1);

namespace Fandoogh\CRM\PartnerPricing;

defined('ABSPATH') || exit;

final class Access
{
    public const USER_META = '_fa_is_partner';

    public static function isPartner(): bool
    {
        $id = get_current_user_id();
        return $id > 0 && get_user_meta($id, self::USER_META, true) === 'yes';
    }

    public static function canManage(int $userId): bool
    {
        return (current_user_can('manage_options') || current_user_can('manage_woocommerce'))
            && current_user_can('edit_user', $userId);
    }
}
