<?php

namespace App\Data;

class Roles extends Permissions
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_MODERATOR = 'ROLE_MODERATOR';
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    public const DEFAULT_USER_PERMISSIONS = [
        self::POST_CREATE,
        self::POST_DELETE,
        self::POST_EDIT,

        self::TOPIC_CREATE,
        self::TOPIC_DELETE,
        self::TOPIC_EDIT,
        self::TOPIC_LOCK,
        self::TOPIC_UNLOCK,
        self::TOPIC_SET_VISIBLE,
        self::TOPIC_SET_HIDDEN,

        self::USER_SET_PRIVATE,
        self::USER_SET_PUBLIC,
        self::USER_VIEW_PROFILE,
    ];

    public const DEFAULT_MODERATOR_PERMISSIONS = [
        ...self::DEFAULT_USER_PERMISSIONS,
        self::USER_BAN,
        self::USER_UNBAN,
    ];

    public const DEFAULT_ADMIN_PERMISSIONS = [
        ...self::DEFAULT_MODERATOR_PERMISSIONS,
        self::USER_CHANGE_ROLE,
        self::USER_BAN_MODIFY,
        self::USER_ADD_PERMISSION,
        self::USER_REVOKE_PERMISSION,
        self::BOARD_CREATE,
        self::BOARD_DELETE,
        self::BOARD_EDIT,
        self::TOPIC_MOVE
    ];

    public function getRoles(): array
    {
        return [
            self::ROLE_USER,
            self::ROLE_MODERATOR,
            self::ROLE_ADMIN,
        ];
    }

    public function getRolesAndPermissions(): array
    {
        return [
            self::ROLE_USER => self::DEFAULT_USER_PERMISSIONS,
            self::ROLE_MODERATOR => self::DEFAULT_MODERATOR_PERMISSIONS,
            self::ROLE_ADMIN => self::DEFAULT_ADMIN_PERMISSIONS,
        ];
    }
}