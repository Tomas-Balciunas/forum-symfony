<?php

namespace App\Data;

use App\Helper\PostHelper;
use App\Repository\TopicRepository;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Messages
{
    public const DEFAULT_PERMISSION_DENIED = 'You do not have permission to perform this action.';
    public const PERMISSION_DENIED = [
        Permissions::POST_CREATE => 'You are not allowed to create posts.',
        Permissions::POST_EDIT => 'You are not allowed to edit this post.',
        Permissions::POST_DELETE => 'You are not allowed to delete this post.',
        Permissions::TOPIC_CREATE => 'You are not allowed to create topics.',
        Permissions::TOPIC_EDIT => 'You are not allowed to edit this topic.',
        Permissions::TOPIC_DELETE => 'You are not allowed to delete this topic.',
        Permissions::TOPIC_LOCK => 'You are not allowed to lock this topic.',
        Permissions::TOPIC_UNLOCK => 'You are not allowed to unlock this topic.',
        Permissions::TOPIC_SET_HIDDEN => 'You are not allowed to hide this topic.',
        Permissions::TOPIC_SET_VISIBLE => 'You are not allowed to set this topic visible.',
        Permissions::BOARD_CREATE => 'You are not allowed to create boards.',
        Permissions::BOARD_DELETE => 'You are not allowed to delete this board.',
        Permissions::BOARD_EDIT => 'You are not allowed to edit this board.',
        Permissions::USER_REVOKE_PERMISSION => 'You are not allowed to revoke permissions.',
        Permissions::USER_ADD_PERMISSION => 'You are not allowed to grant permissions.',
        Permissions::USER_BAN => 'You are not allowed to suspend users.',
        Permissions::USER_UNBAN => 'You are not allowed to lift suspensions.',
        Permissions::USER_CHANGE_ROLE => 'You are not allowed to change roles.',
        Permissions::USER_SET_PRIVATE => 'You are not allowed to set profiles private.',
        Permissions::USER_SET_PUBLIC => 'You are not allowed to set profiles public.',
        Permissions::USER_VIEW_PROFILE => 'You are not allowed to view this profile.',
    ];

    public function __construct(
        private UserRepository $userRepository,
        private TopicRepository $topicRepository,
        private UrlGeneratorInterface $urlGenerator,
        private PostHelper $postHelper
    )
    {
    }

    public function getErrMsg(string $key): string
    {
        return self::PERMISSION_DENIED[$key] ?? self::DEFAULT_PERMISSION_DENIED;
    }

    public function getFormattedNotification(string $template, array $data): string
    {
        return match ($template) {
            'TOPIC_REPLY' => $this->topicReplyTemplate($data),
        };
    }

    public function topicReplyTemplate(array $data): string
    {
        $user = $this->userRepository->findOneBy(['id' => $data['user']]);
        $topic = $this->topicRepository->findOneBy(['id' => $data['topic']]);
        $userPath = $this->urlGenerator->generate('user_profile_public', ['id' => $user->getId()]);
        $topicPath = $this->urlGenerator->generate('topic_show_paginated', [
            'id' => $topic->getId(),
            'page' => $this->postHelper->getPostPosition($data['post'], $topic->getId())
        ]) . '#' . $data['post'];

        return sprintf(
            '<a href="%s" class="text-yellow-400 hover:underline">%s</a> has replied to your topic <a href="%s" class="text-yellow-400 hover:underline">%s</a>',
            $userPath,
            $user->getUsername(),
            $topicPath,
            $topic->getTitle()
        );
    }
}