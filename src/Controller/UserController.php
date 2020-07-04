<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\User;
use App\Form\ProjectType;
use App\Form\UserUpdateType;
use App\Service\EmailService;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class UserController
 * @package App\Controller
 * @Route("/secured/users",name="users_")
 */
class UserController extends AbstractController
{
    /**
     * Lists all Users.
     * @Rest\Get("/usersList",name="userList")
     * @Rest\View()
     * @return JsonResponse|Response
     * @SWG\Response(
     *     response=200,
     *     description="get all sers"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * @SWG\Tag(name="Users")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function getAllUsers()
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $allPriority = $repository->findall();
        $serializer = SerializerBuilder::create()->build();
        $data = $serializer->serialize($allPriority, 'json');
        $response = new Response($data);
        return $response;
    }

    /**
     * @param Request $request
     * @Rest\View()
     * @Rest\Get("/userShow/{username}")
     * @return Response
     * @SWG\Response(
     *     response=200,
     *     description="get one user",
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * @SWG\Tag(name="Users")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function getOneUser(Request $request, string $username)
    {

        $repository = $this->getDoctrine()->getRepository(User::class);

        $user = $repository->findOneBy([
            'username' => $username
        ]);
        if (empty($user)) {
            return new JsonResponse(['status' => 'Expedition not Found'], Response::HTTP_NOT_FOUND);
        }
        $data = SerializerBuilder::create()->build()->serialize($user, 'json');

        $response = new Response($data);
        return $response;
    }

    /**
     * @return Response
     * @Rest\View()
     * @Rest\Get("/AllRoles")
     * @SWG\Response(
     *     response=200,
     *     description="get all roles"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * @SWG\Tag(name="Users")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function getAllRoles()
    {
        $originalRoles = $this->getParameter('security.role_hierarchy.roles');
        $serializer = new Serializer([new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter())]);

        $data = $serializer->normalize($originalRoles, null, [AbstractNormalizer::ATTRIBUTES => ['id','name','color'],
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }]);

        return new JsonResponse(
            [
                'status' => 'ok',
                'data'=>$data
            ],
            JsonResponse::HTTP_CREATED
        );

    }


    /**
     * update User
     * @param Request $request
     * @Rest\View()
     * @Rest\Patch("/UserUpdate/{id}")
     * @return FormInterface|Response
     * @SWG\Response(
     *     response=200,
     *     description="update user",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class, groups={"full"}))
     *     )
     * )
     *  * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * @SWG\Tag(name="Users")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function putUser(Request $request, int $id, EmailService $mailer)
    {
        $repository = $this->getDoctrine()->getRepository(User::class);

        $user = $repository->find($id);
        if (empty($user)) {
            return new JsonResponse(['status' => 'Expedition not Found'], JsonResponse::HTTP_NOT_FOUND);
        }
        $data = json_decode(
            $request->getContent(),
            true
        );
        $form = $this->createForm(UserUpdateType::class, $user);

        $form->submit($data);

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($form->getData());
        $entityManager->flush();
        $mailer->sendEmail($user->getEmail(), $this->render('email.html.twig', ['title' => "Password Just Changed", 'content' => "Hi " . $user->getUsername() . ",

You recently updated the password for your Twitch account. If this was you, then no further action is required."]));
        return new JsonResponse(['status' => 'ok',], JsonResponse::HTTP_OK);
    }
}
