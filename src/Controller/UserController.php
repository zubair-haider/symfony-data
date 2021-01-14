<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends ApiController
{
    public function login(Request $request)
    {
        try {
            $user = $this->getUser();
            $data = [
                'username' => $user->getEmail(),
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
                return $this->respondValidationError("Invalid Roles or Password or Email");
            }
            $user = new User($email);
            $user->setPassword($encoder->encodePassword($user, $password));
            $em->persist($user);
            $em->flush();
            return $this->respondWithSuccess(sprintf('User %s successfully created', 'a'));
        } catch (\Exception $e) {
            return $this->respondValidationError($e->getMessage());
        }
    }
}
