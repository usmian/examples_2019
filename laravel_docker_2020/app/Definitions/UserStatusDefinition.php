<?php

namespace App\Definitions;

/**
 * Class UserStatusDefinition
 * @package App\Definitions
 */
class UserStatusDefinition
{
    const STATUS_WAIT = 'wait'; // Письмо отправлено, ожидает подтверждения
    const STATUS_ACTIVE = 'active';// аквтивен
    const STATUS_BANNED_TEMPORARILY = 'banned-temporarily';// Временный бан
    const STATUS_BANNED_PERMANENTLY = 'banned-permanently';// Постоянный бан
    const STATUS_SELF_BLOCKED = 'self-blocked';// Пользователь удалил страничку
}