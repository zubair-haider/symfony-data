<?php

namespace App\Controller;

use App\Entity\Auth;
use App\Entity\User;
use App\Repository\AuthRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AuthController extends ApiController
{
    public function login(Request $request, AuthRepository $authRepository)
    {
        try {
            $user = $this->getUser();
            $authObj = $authRepository->findOneBy(['userId' => $user->getId()]);
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

    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $request = $this->transformJsonBody($request);
            $password = $request->get('password');
            $email = $request->get('email');
            if (empty($password) || empty($email)) {
                return $this->respondValidationError("Invalid Password or Email");
            }
            $user = new User();
            $user->setPassword($encoder->encodePassword($user, $password));
            $user->setRoles(['USER_ROLE']);
            $user->setEmail($email);
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

    public function createAuth(Request $request, AuthRepository $authRepository)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $request = $this->transformJsonBody($request);
            $token = $request->get('token');
            $userId = $request->get('userId');
            if (empty($token) || empty($userId)) {
                return $this->respondValidationError("Invalid Token or user Id");
            }
            $auth = $authRepository->findOneBy(['userId' => $userId]);
            if ($auth) {
                $auth->setToken($token);
            } else {
                $auth = new Auth();
                $auth->setUserId($userId);
                $auth->setToken($token);
                $em->persist($auth);
                $em->flush();
            }
            return $this->respondWithSuccess("Token saved");
        } catch (\Exception $e) {
            return $this->respondValidationError($e->getMessage());
        }
    }

}
