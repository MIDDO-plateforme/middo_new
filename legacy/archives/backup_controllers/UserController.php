<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/users', name: 'app_users')]
    public function list(Request $request, UserRepository $userRepository): Response
    {
        $typeFilter = $request->query->get('type');
        
        if ($typeFilter) {
            $users = $userRepository->findBy(['userType' => $typeFilter]);
        } else {
            $users = $userRepository->findAll();
        }
        
        $userCounts = [
            'entrepreneur' => $userRepository->count(['userType' => 'entrepreneur']),
            'investisseur' => $userRepository->count(['userType' => 'investisseur']),
            'entreprise' => $userRepository->count(['userType' => 'entreprise']),
            'particulier' => $userRepository->count(['userType' => 'particulier']),
            'association' => $userRepository->count(['userType' => 'association']),
            'institution' => $userRepository->count(['userType' => 'institution']),
            'inspecteur' => $userRepository->count(['userType' => 'inspecteur']),
        ];
        
        return $this->render('user/list.html.twig', [
            'users' => $users,
            'currentFilter' => $typeFilter,
            'userCounts' => $userCounts,
        ]);
    }
    
    #[Route('/user/{id}', name: 'app_user_show', requirements: ['id' => '\d+'])]
    public function show(int $id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);
        
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }
        
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }
}
