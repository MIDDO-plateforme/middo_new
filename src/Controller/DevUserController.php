<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class DevUserController extends AbstractController
{
    #[Route('/dev/create-admin', name: 'dev_create_admin')]
    public function createAdmin(EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        $user = new User();
        $user->setEmail('admin@middo.com');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setFirstName('Admin');
        $user->setLastName('User');
        $user->setUserType('admin');
        $user->setPretAVousExporter(false); // ⭐ CHAMP OBLIGATOIRE
        $user->setPassword($hasher->hashPassword($user, 'admin123'));

        $em->persist($user);
        $em->flush();

        return new Response('Admin created');
    }
}
