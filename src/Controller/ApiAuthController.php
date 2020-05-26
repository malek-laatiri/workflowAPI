<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\EmailService;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\UserBundle\Model\UserManagerInterface;
use phpDocumentor\Reflection\Types\Boolean;
use Swift_Mailer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Validation;


class ApiAuthController extends FOSRestController
{
    /**
     * @Rest\Post("/register", name="api_auth_register")
     * @param Request $request
     * @param Swift_Mailer $mailer
     * @param UserManagerInterface $userManager
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function register(Request $request, EmailService $mailer, UserManagerInterface $userManager)
    {
        $data = json_decode(
            $request->getContent(),
            true
        );
        $validator = Validation::createValidator();
        $constraint = new Collection(array(
            // the keys correspond to the keys in the input array
            'username' => new Length(array('min' => 1)),
            'email' => new Email(),
            'roles' => Array(),
            'privilege'=>new NotNull()
        ));
        $violations = $validator->validate($data, $constraint);
        if ($violations->count() > 0) {
            return new JsonResponse(["error" => (string)$violations], 500);
        }


        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = ''; //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 15; $i++) {
            $n = rand(0, $alphaLength);
            $pass = $pass . $alphabet[$n];
        }
        $roles = [];
        $username = $data['username'];
        $email = $data['email'];
        $privilege=$data['privilege'];
        array_push($roles, $data['roles']);
        $user = new User();
        $user
            ->setUsername($username)
            ->setPlainPassword($pass)
            ->setEmail($email)
            ->setEnabled(true)
            ->setRoles($data['roles'])
            ->setPrivilege($privilege)
            ->setSuperAdmin(false);
        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        } catch (\Exception $e) {
            return new JsonResponse(["error" => $e->getMessage()], 500);
        }


        $mailer->sendEmail($user->getEmail(),$this->render('email.html.twig', ['title' => "Account Activation", 'content' => "your username ".$user->getUsername()." you passsword ".$pass]));



        return new JsonResponse(["status" => "done"], JsonResponse::HTTP_CREATED);

    }

    /**
     * @Route("/api/login_check", name="api_login_check")
     */
    public function login()
    {
        dump($this->getUser());
    }
}