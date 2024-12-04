<?php

namespace App\Data;

class Permissions
{
    public const BOARD_CREATE = 'board.create';
    public const BOARD_DELETE = 'board.delete';
    public const BOARD_EDIT = 'board.edit';

    public const POST_CREATE = 'post.create';
    public const POST_DELETE = 'post.delete';
    public const POST_EDIT = 'post.edit';

    public const TOPIC_CREATE = 'topic.create';
    public const TOPIC_DELETE = 'topic.delete';
    public const TOPIC_EDIT = 'topic.edit';
    public const TOPIC_LOCK = 'topic.lock';
    public const TOPIC_UNLOCK = 'topic.unlock';
    public const TOPIC_SET_VISIBLE = 'topic.set.visible';
    public const TOPIC_SET_HIDDEN = 'topic.set.hidden';

    public const USER_BAN = 'user.ban';
    public const USER_UNBAN = 'user.unban';
    public const USER_SET_PRIVATE = 'user.set.private';
    public const USER_SET_PUBLIC = 'user.set.public';
    public const USER_CHANGE_ROLE = 'user.change.role';
    public const USER_ADD_PERMISSION = 'user.add.permission';
    public const USER_REVOKE_PERMISSION = 'user.revoke.permission';

    public static function getPermissions(): array
    {
        return [
            self::BOARD_CREATE,
            self::BOARD_DELETE,
            self::BOARD_EDIT,
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
            self::USER_BAN,
            self::USER_UNBAN,
            self::USER_SET_PRIVATE,
            self::USER_SET_PUBLIC,
            self::USER_CHANGE_ROLE,
            self::USER_ADD_PERMISSION,
            self::USER_REVOKE_PERMISSION,
        ];
    }

}