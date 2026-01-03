<?php

namespace App\Twig;

use App\Repository\MessageRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UnreadMessagesExtension extends AbstractExtension
{
    private MessageRepository $messageRepository;

    public function __construct(MessageRepository $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('unread_messages_count', [$this, 'getUnreadMessagesCount']),
        ];
    }

    public function getUnreadMessagesCount(): int
    {
        return $this->messageRepository->countUnreadMessages();
    }
}
