<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use App\Service\EmailService;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


/**
 * Class ProjectController
 * @package App\Controller
 * @Route("/secured/project",name="project_")
 */
class ProjectController extends FOSRestController
{
    /**
     * get all the projects of connected user
     * @return Response
     * @Rest\Get("/projectList/{userid}")
     * @SWG\Response(
     *     response=200,
     *     description="get all projects",
     *     @SWG\Schema(
     *                   type="object",
     * @SWG\Property(property="id",type="integer",description="ID"),
     * @SWG\Property(property="name",type="string",description="name"),
     * @SWG\Property(property="startdate",type="Date",description="startdate"),
     * @SWG\Property(property="duedate",type="date",description="duedate"),
     *          @SWG\Property(property="createdBy",type="array",@Model(type="App\Entity\User"),description="User"),
     * @SWG\Property(property="backlog",type="array",@Model(type="App\Entity\Backlog"),description="backlog of the project"),
     * @SWG\Property(property="Team",type="array",@Model(type="App\Entity\User"),description="List of users in the same projects"),
     * @SWG\Property(property="done",type="boolean",description="duedate"),
     *
     * )
     *     )
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * @SWG\Tag(name="Projects")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function getAllProjects(int $userid)
    {
        $repository = $this->getDoctrine()->getRepository(Project::class);
        $allProjects = $repository->findBy(['createdBy' => $userid]);
        $serializer = SerializerBuilder::create()->build();
        $data = $serializer->serialize($allProjects, 'json', SerializationContext::create()->enableMaxDepthChecks());
        $response = new Response($data);
        return $response;
    }


    /**
     * get all the projects of connected user
     * @param int $userid
     * @return Response
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     * @Rest\Get("/projectListPrime/{userid}")
     * @SWG\Response(
     *     response=200,
     *     description="get all projects",
     *     @SWG\Schema(
     *                   type="object",
     * @SWG\Property(property="id",type="integer",description="ID"),
     * @SWG\Property(property="name",type="string",description="name"),
     * @SWG\Property(property="startdate",type="Date",description="startdate"),
     * @SWG\Property(property="duedate",type="date",description="duedate"),
     *          @SWG\Property(property="createdBy",type="array",@Model(type="App\Entity\User"),description="User"),
     * @SWG\Property(property="backlog",type="array",@Model(type="App\Entity\Backlog"),description="backlog of the project"),
     * @SWG\Property(property="Team",type="array",@Model(type="App\Entity\User"),description="List of users in the same projects"),
     * @SWG\Property(property="done",type="boolean",description="duedate"),
     *
     * )
     *     )
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * @SWG\Tag(name="Projects")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function getAllProjectsPrime(int $userid)
    {
        $repository = $this->getDoctrine()->getRepository(Project::class);
        $allProjects = $repository->findBy(['createdBy' => $userid]);
        $serializer = new Serializer([new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter())]);

        $data = $serializer->normalize($allProjects, null, [AbstractNormalizer::ATTRIBUTES => ['id', 'name', 'done', 'startDate', 'dueDate',
            'backlog' => ['id', 'userStories' => ['id', 'subject', 'content'
                , 'activity' => ['id', 'name'],
                'isComfirmed', 'isVerified', 'dueDate',
                'progress'

            ],],
            'role'],
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }]);
        return new JsonResponse(
            [
                'status' => 'ok',
                'data' => $data
            ],
            JsonResponse::HTTP_CREATED
        );
    }


    /**
     * Create new Project.
     * @Rest\Post("/projectCreate")
     * @param Request $request
     * @return \FOS\RestBundle\View\View|JsonResponse
     * @SWG\Response(
     *     response=200,
     *     description="post new project"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * @SWG\Tag(name="Projects")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function postProject(Request $request, EmailService $mailer)
    {
        $data = json_decode(
            $request->getContent(),
            true
        );
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->submit($data);
        if (!$form->isValid()) {
            return new JsonResponse(
                [
                    'status' => 'Expedition not Found',
                    'errors' => (string)$form->getErrors(true, false),
                    'form' => $form
                ],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
        foreach ($form->getData()->getTeam() as &$value) {
            $mailer->sendEmail($value->getEmail(), $this->render('email.html.twig', ['title' => "New Project", 'content' => " you are assigned to a new project "]));


        }
        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($form->getData());
        $entityManager->flush();

        return new JsonResponse(
            [
                'status' => 'ok',
            ],
            JsonResponse::HTTP_CREATED
        );
    }

    /**
     * update project
     * @param Request $request
     * @Rest\View()
     * @Rest\Patch("/ProjectUpdate/{id}")
     * @return \Symfony\Component\Form\FormInterface|Response
     * @SWG\Response(
     *     response=200,
     *     description="update project",
     *    @SWG\Schema(
     *                   type="object",
     * @SWG\Property(property="id",type="integer",description="ID"),
     * @SWG\Property(property="name",type="string",description="name"),
     * @SWG\Property(property="startdate",type="Date",description="startdate"),
     * @SWG\Property(property="duedate",type="date",description="duedate"),
     * @SWG\Property(property="createdBy",type="array",@Model(type="App\Entity\User"),description="User"),
     * @SWG\Property(property="backlog",type="array",@Model(type="App\Entity\Backlog"),description="backlog of the project"),
     * @SWG\Property(property="Team",type="array",@Model(type="App\Entity\User"),description="List of users in the same projects"),
     * @SWG\Property(property="done",type="boolean",description="duedate")
     * ))
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * @SWG\Tag(name="Projects")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function putProject(Request $request, int $id)
    {
        $repository = $this->getDoctrine()->getRepository(Project::class);

        $project = $repository->find($id);
        if (empty($project)) {
            return new JsonResponse(
                [
                    'status' => 'Expedition not Found',
                ],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
        $data = json_decode(
            $request->getContent(),
            true
        );
        $form = $this->createForm(ProjectType::class, $project);

        $form->submit($data);

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($form->getData());
        $entityManager->flush();
        return new JsonResponse(['status' => 'ok',], JsonResponse::HTTP_OK);
    }

    /**
     * get one project
     * @param Request $request
     * @Rest\View()
     * @Rest\Get("/ProjectShow/{id}")
     * @return Response
     * @SWG\Response(
     *     response=200,
     *     description="Returns one project",
     *    @SWG\Schema(
     *                   type="object",
     * @SWG\Property(property="id",type="integer",description="ID"),
     * @SWG\Property(property="name",type="string",description="name"),
     * @SWG\Property(property="startdate",type="Date",description="startdate"),
     * @SWG\Property(property="duedate",type="date",description="duedate"),
     *          @SWG\Property(property="createdBy",type="array",@Model(type="App\Entity\User"),description="User"),
     * @SWG\Property(property="backlog",type="array",@Model(type="App\Entity\Backlog"),description="backlog of the project"),
     * @SWG\Property(property="Team",type="array",@Model(type="App\Entity\User"),description="List of users in the same projects"),
     * @SWG\Property(property="done",type="boolean",description="duedate"),
     *
     * )
     *
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * @SWG\Tag(name="Projects")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function getOneProject(Request $request, int $id)
    {

        $repository = $this->getDoctrine()->getRepository(Project::class);

        $project = $repository->find($id);
        if (empty($project)) {
            return new JsonResponse(['status' => 'Expedition not Found'], Response::HTTP_NOT_FOUND);
        }
        $data = SerializerBuilder::create()->build()->serialize($project, 'json');

        $response = new Response($data);
        return $response;
    }

    /**
     * delete a project
     * @param int $id
     * @return View|Response
     * @Rest\Delete("/projectDelete/{id}")
     * @Rest\View()
     * @SWG\Response(
     *     response=200,
     *     description="ok",
     *
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * @SWG\Tag(name="Projects")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function deleteProject(int $id)
    {
        $project = $this->getDoctrine()->getRepository(Project::class)->find($id);
        if (empty($project)) {
            return new JsonResponse(['status' => 'Expedition not Found'], JsonResponse::HTTP_NOT_FOUND);
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($project);
        $em->flush();

        return new JsonResponse(['status' => 'ok'],
            JsonResponse::HTTP_OK
        );
    }

    /**
     * get the projects of one user
     * @return Response
     * @Rest\Get("/projectsByUser/{id}")
     * @SWG\Response(
     *     response=200,
     *     description="get all the projects of one user",
     *     @SWG\Schema(
     *                   type="object",
     * @SWG\Property(property="id",type="integer",description="ID"),
     * @SWG\Property(property="name",type="string",description="name"),
     * @SWG\Property(property="startdate",type="Date",description="startdate"),
     * @SWG\Property(property="duedate",type="date",description="duedate"),
     *          @SWG\Property(property="createdBy",type="array",@Model(type="App\Entity\User"),description="User"),
     * @SWG\Property(property="backlog",type="array",@Model(type="App\Entity\Backlog"),description="backlog of the project"),
     * @SWG\Property(property="Team",type="array",@Model(type="App\Entity\User"),description="List of users in the same projects"),
     * @SWG\Property(property="done",type="boolean",description="duedate"),
     *
     * )
     *     )
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * @SWG\Tag(name="Projects")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function getProjectsOfOneUser($id, ProjectRepository $repository)
    {
        $allProjects = $repository->findProjectsOfOneUser($id);
        $serializer = SerializerBuilder::create()->build();
        $data = $serializer->serialize($allProjects, 'json', SerializationContext::create()->enableMaxDepthChecks());
        $response = new Response($data);
        return $response;
    }


    /**
     * get the project Team->TeamLeader/developers/testers/client
     * @return Response
     * @Rest\Get("/projectTeam/{id}")
     * @SWG\Response(
     *     response=200,
     *     description="get the project Team->TeamLeader/developers/testers/client",
     *     @SWG\Schema(
     *                   type="object",
     * @SWG\Property(property="id",type="integer",description="ID"),
     * @SWG\Property(property="name",type="string",description="name"),
     * @SWG\Property(property="startdate",type="Date",description="startdate"),
     * @SWG\Property(property="duedate",type="date",description="duedate"),
     * @SWG\Property(property="createdBy",type="array",@Model(type="App\Entity\User"),description="User"),
     * @SWG\Property(property="backlog",type="array",@Model(type="App\Entity\Backlog"),description="backlog of the project"),
     * @SWG\Property(property="Team",type="array",@Model(type="App\Entity\User"),description="List of users in the same projects"),
     * @SWG\Property(property="done",type="boolean",description="duedate")
     *
     * )
     *     )
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * @SWG\Tag(name="Projects")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function getProjectTeam($id, UserRepository $userRepository)
    {
        $ProjectTeam = $userRepository->findProjectTeam($id);
        $serializer = SerializerBuilder::create()->build();
        $data = $serializer->serialize($ProjectTeam, 'json', SerializationContext::create()->enableMaxDepthChecks());
        $response = new Response($data);
        return $response;
    }

    /**
     * get the current projects of one user
     * @return Response
     * @Rest\Get("/currentProjectsByUser/{id}")
     * @SWG\Response(
     *     response=200,
     *     description="get all the projects of one user",
     *     @SWG\Schema(
     *                   type="object",
     * @SWG\Property(property="id",type="integer",description="ID"),
     * @SWG\Property(property="name",type="string",description="name"),
     * @SWG\Property(property="startdate",type="Date",description="startdate"),
     * @SWG\Property(property="duedate",type="date",description="duedate"),
     *          @SWG\Property(property="createdBy",type="array",@Model(type="App\Entity\User"),description="User"),
     * @SWG\Property(property="backlog",type="array",@Model(type="App\Entity\Backlog"),description="backlog of the project"),
     * @SWG\Property(property="Team",type="array",@Model(type="App\Entity\User"),description="List of users in the same projects"),
     * @SWG\Property(property="done",type="boolean",description="duedate"),
     *
     * )
     *     )
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * @SWG\Tag(name="Projects")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function getCurrentProjectsOfOneUser($id, ProjectRepository $repository)
    {
        $allProjects = $repository->findCurrentProjectsOfOneUser($id);
        $serializer = SerializerBuilder::create()->build();
        $data = $serializer->serialize($allProjects, 'json', SerializationContext::create()->enableMaxDepthChecks());
        $response = new Response($data);
        return $response;
    }

    /**
     * get statistics of one project
     * @param Request $request
     * @Rest\View()
     * @Rest\Get("/ProjectStatistics/{id}")
     * @return Response
     * @SWG\Response(
     *     response=200,
     *     description="Returns one project",
     *    @SWG\Schema(
     *                   type="object",
     * @SWG\Property(property="id",type="integer",description="ID"),
     * @SWG\Property(property="name",type="string",description="name"),
     * @SWG\Property(property="startdate",type="Date",description="startdate"),
     * @SWG\Property(property="duedate",type="date",description="duedate"),
     *          @SWG\Property(property="createdBy",type="array",@Model(type="App\Entity\User"),description="User"),
     * @SWG\Property(property="backlog",type="array",@Model(type="App\Entity\Backlog"),description="backlog of the project"),
     * @SWG\Property(property="Team",type="array",@Model(type="App\Entity\User"),description="List of users in the same projects"),
     * @SWG\Property(property="done",type="boolean",description="duedate"),
     *
     * )
     *
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * @SWG\Tag(name="Projects")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function getStatisticsProject(Request $request, int $id)
    {

        $repository = $this->getDoctrine()->getRepository(Project::class);

        $project = $repository->findOneBy(['id' => $id]);
        if (empty($project)) {
            return new JsonResponse(['status' => 'Expedition not Found'], Response::HTTP_NOT_FOUND);
        }
        $serializer = new Serializer([new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter())]);

        $data = $serializer->normalize($project->getBacklog(), null, [AbstractNormalizer::ATTRIBUTES => ['id','name',
            'userStories'=>['id','subject'
                ,'activity'=>['id','name'],
                'isComfirmed','isVerified',
                'progress'

            ],],
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
     * update project
     * @param Request $request
     * @Rest\Patch("/ProjectDone/{id}")
     * @return \Symfony\Component\Form\FormInterface|Response
     * @SWG\Response(
     *     response=200,
     *     description="update project",
     *    @SWG\Schema(
     *                   type="object",
     * @SWG\Property(property="id",type="integer",description="ID"),
     * @SWG\Property(property="name",type="string",description="name"),
     * @SWG\Property(property="startdate",type="Date",description="startdate"),
     * @SWG\Property(property="duedate",type="date",description="duedate"),
     * @SWG\Property(property="createdBy",type="array",@Model(type="App\Entity\User"),description="User"),
     * @SWG\Property(property="backlog",type="array",@Model(type="App\Entity\Backlog"),description="backlog of the project"),
     * @SWG\Property(property="Team",type="array",@Model(type="App\Entity\User"),description="List of users in the same projects"),
     * @SWG\Property(property="done",type="boolean",description="duedate")
     * ))
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * @SWG\Tag(name="Projects")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function putDoneProject(Request $request, int $id)
    {
        $repository = $this->getDoctrine()->getRepository(Project::class);

        $project = $repository->find($id);
        if (empty($project)) {
            return new JsonResponse(
                [
                    'status' => 'Expedition not Found',
                ],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
        $data = json_decode(
            $request->getContent(),
            true
        );
        $project->setDone(true);

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($project);
        $entityManager->flush();
        return new JsonResponse(['status' => 'ok',], JsonResponse::HTTP_OK);
    }
}
