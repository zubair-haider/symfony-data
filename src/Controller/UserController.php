<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends ApiController
{
    public function userProfile(UserRepository $userRepository)
    {
        try {
            $user = $this->getUser();
            if (!$user) {
                return $this->respondValidationError("User not logged in");
            }

            $data = [
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ];
            return $this->respondWithSuccess($data);
        } catch (\Exception $e) {
            return $this->respondValidationError($e->getMessage());
        }
    }

    public function userProfileUpdate(Request $request)
    {
        try {
            $user = $this->getUser();

            if (!$user) {
                return $this->respondValidationError("User not logged in");
            }

            $request = $this->transformJsonBody($request);
            $roles = $request->get('roles');
            $user->setRoles($roles);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->respondWithSuccess("User updated");
        } catch (\Exception $e) {
            return $this->respondValidationError($e->getMessage());
        }
    }

    public function userProfileDelete(Request $request)
    {
        try {
            $user = $this->getUser();

            if (!$user) {
                return $this->respondValidationError("User not logged in");
            }

            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
            return $this->respondWithSuccess("User deleted");
        } catch (\Exception $e) {
            return $this->respondValidationError($e->getMessage());
        }
    }

    public function showUser($id, UserRepository $userRepository)
    {
        try {
            $user = $userRepository->findOneBy(['id' => $id]);
            $data = [
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ];
            return $this->respondWithSuccess($data);
        } catch (\Exception $e) {
            return $this->respondValidationError($e->getMessage());
        }
    }

    public function updateUser($id, UserRepository $userRepository, Request $request)
    {
        try {
            $user = $userRepository->findOneBy(['id' => $id]);

            $request = $this->transformJsonBody($request);
            $roles = $request->get('roles');
            $user->setRoles($roles);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->respondWithSuccess("User updated");
        } catch (\Exception $e) {
            return $this->respondValidationError($e->getMessage());
        }
    }

    public function createUser(Request $request, UserPasswordEncoderInterface $encoder)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $request = $this->transformJsonBody($request);
            $password = $request->get('password');
            $email = $request->get('email');
            $roles = $request->get('roles');
            if (empty($password) || empty($email)) {
                return $this->respondValidationError("Invalid Password or Email");
            }
            $user = new User($email);
            $user->setPassword($encoder->encodePassword($user, $password));
            $user->setEmail($email);
            $user->setRoles($roles);
            $em->persist($user);
            $em->flush();
            $data = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ];
            return $this->respondWithSuccess($data);
        } catch (\Exception $e) {
            return $this->respondValidationError($e->getMessage());
        }
    }

    public function deleteUser($id, UserRepository $userRepository)
    {
        try {
            $user = $userRepository->findOneBy(['id' => $id]);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
            return $this->respondWithSuccess("user deleted");
        } catch (\Exception $e) {
            return $this->respondValidationError($e->getMessage());
        }
    }

}
