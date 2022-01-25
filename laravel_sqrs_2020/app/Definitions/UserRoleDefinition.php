<?php
namespace App\Definitions;

/**
 * Справочник ролей пользователя
 *
 * Class UserRoleDefinition
 * @package App\Definitions
 */
class UserRoleDefinition
{
    const USER_ROLE = 'user-role';// Публичная регистрация. Стандартный пользователь с подтверждение емэйла и телефона

    const ADVANCED_USER_ROLE = 'advanced-user-role';// Публичная регистрация. Заполнена информация о себе, выбрана специальность

    const FULL_USER_ROLE = 'full-user-role';// Публичная регистрация. Полная верификация личности(фото с разворотом паспорта и пропиской)

    const ADMIN_ROLE = 'admin-role';// Внутренний администратор

    const SUPERADMIN_ROLE = 'super-admin-role';// Внутренний администратор с полными правами
}
