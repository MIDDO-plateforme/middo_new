<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/messages')]
class MessageController extends AbstractController
{
    #[Route('', name: 'app_messages', methods: ['GET'])]
    public function list(MessageRepository $messageRepo, UserRepository $userRepo): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $currentUser = $this->getUser();

        // Récupérer tous les utilisateurs avec qui on a échangé
        $conversations = [];
        
        // Messages reçus
        $receivedMessages = $messageRepo->findBy(['recipient' => $currentUser]);
        foreach ($receivedMessages as $msg) {
            $userId = $msg->getSender()->getId();
            if (!isset($conversations[$userId])) {
                $conversations[$userId] = [
                    'user' => $msg->getSender(),
                    'lastMessage' => $msg,
                    'unreadCount' => 0
                ];
            }
            if (!$msg->isRead() && $msg->getRecipient()->getId() === $currentUser->getId()) {
                $conversations[$userId]['unreadCount']++;
            }
            if ($msg->getCreatedAt() > $conversations[$userId]['lastMessage']->getCreatedAt()) {
                $conversations[$userId]['lastMessage'] = $msg;
            }
        }

        // Messages envoyés
        $sentMessages = $messageRepo->findBy(['sender' => $currentUser]);
        foreach ($sentMessages as $msg) {
            $userId = $msg->getRecipient()->getId();
            if (!isset($conversations[$userId])) {
                $conversations[$userId] = [
                    'user' => $msg->getRecipient(),
                    'lastMessage' => $msg,
                    'unreadCount' => 0
                ];
            } elseif ($msg->getCreatedAt() > $conversations[$userId]['lastMessage']->getCreatedAt()) {
                $conversations[$userId]['lastMessage'] = $msg;
            }
        }

        // Trier par date du dernier message
        usort($conversations, function($a, $b) {
            return $b['lastMessage']->getCreatedAt() <=> $a['lastMessage']->getCreatedAt();
        });

        return $this->render('message/list.html.twig', [
            'conversations' => $conversations
        ]);
    }

    #[Route('/{id}', name: 'app_message_chat', methods: ['GET', 'POST'])]
    public function chat(
        int $id, 
        UserRepository $userRepo, 
        MessageRepository $messageRepo,
        EntityManagerInterface $em,
        Request $request
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $currentUser = $this->getUser();
        $otherUser = $userRepo->find($id);

        if (!$otherUser) {
            throw $this->createNotFoundException('Utilisateur introuvable');
        }

        // Récupérer tous les messages entre les deux utilisateurs
        $messages = $messageRepo->findConversationBetween($currentUser, $otherUser);

        // Marquer les messages reçus comme lus
        $messageRepo->markConversationAsRead($currentUser, $otherUser);
        $em->flush();

        // Traiter l'envoi d'un nouveau message
        if ($request->isMethod('POST')) {
            $content = $request->request->get('content');
            
            if (!empty(trim($content))) {
                $message = new Message();
                $message->setSender($currentUser);
                $message->setRecipient($otherUser);
                $message->setContent($content);
                
                $em->persist($message);
                $em->flush();

                $this->addFlash('success', 'Message envoyé !');
                return $this->redirectToRoute('app_message_chat', ['id' => $id]);
            }
        }

        return $this->render('message/chat.html.twig', [
            'otherUser' => $otherUser,
            'messages' => $messages
        ]);
    }

    #[Route('/unread/count', name: 'app_messages_unread_count', methods: ['GET'])]
    public function unreadCount(MessageRepository $messageRepo): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $count = $messageRepo->countUnreadMessages($this->getUser());
        
        return $this->json(['count' => $count]);
    }
}
