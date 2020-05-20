<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Form\CommentsType;
use App\Repository\CommentsRepository;
use App\Repository\UserStoryRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/secured/comments", name="comments_")
 *
 */
class CommentsController extends FOSRestController
{

    /**
     * @Rest\Get("/{id}")
     */
    public function index(int $id)
    {
        $repository = $this->getDoctrine()->getRepository(Comments::class);
        $comment = $repository->findBy(['userStory'=>$id]);
        $serializer = SerializerBuilder::create()->build();

        $data = $serializer->serialize($comment, 'json');
        $response = new Response($data);
        return $response;
    }

    /**
     * @Rest\Post("/newComment/{id}")
     */
    public function new(Request $request, int $id, UserStoryRepository $userStoryRepository)
    {
        $data = json_decode(
            $request->getContent(),
            true
        );
        $userstory = $userStoryRepository->find($id);
        $comment = new Comments();
        $comment->setUserStory($userstory);
        $form = $this->createForm(CommentsType::class, $comment);

        $form->submit($data);
        if (!$form->isValid()) {
            return new JsonResponse(
                [
                    'status' => 'Expedition not Found',
                    'errors' => (string)$form->getErrors(true, false)
                ],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($form->getData());
        $entityManager->flush();

        return new JsonResponse(['status' => 'ok',], JsonResponse::HTTP_CREATED);
    }


}
