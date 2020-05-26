<?php

namespace App\Controller;

use App\Entity\Board;
use App\Entity\Task;
use App\Normalizers\Normalizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Serializer;

class TaskController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(
        EntityManagerInterface $entityManager,
        Normalizer $taskNormalizer
    )
    {
        $this->em = $entityManager;
        $this->serializer = $taskNormalizer;
    }

    /**
     * @Route("/task/{id}", name="task_create", methods={"POST"})
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    public function create(Request $request, string $id)
    {
        $data = json_decode($request->getContent(), true);
        $board = $this->em->getRepository(Board::class)->find($id);

        $task = new Task(
            $data['title'],
            $data['description'],
            $board
        );

        $this->em->persist($task);
        $this->em->flush();

        return new JsonResponse(
            ["task" => $this->serializer->normalize($task)],
            Response::HTTP_CREATED
        );
    }

    /**
     * @Route("/tasks", name="task_show_all", methods={"GET"})
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    public function fetchAll()
    {
        $data = $this->em->getRepository(Task::class)->findAll();
        return new JsonResponse(
            ["tasks" => $this->serializer->normalize($data)],
            Response::HTTP_OK
        );
    }

    /**
     * @Route("/task/{id}", name="task_show_one", methods={"GET"})
     * @param string $id
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    public function fetch(string $id)
    {
        $data = $this->em->getRepository(Task::class)->find($id);

        if (!$data) {
            throw $this->createNotFoundException(
                'No task found for id '.$id
            );
        }
        return new JsonResponse(
            ["task" => $this->serializer->normalize($data)],
            Response::HTTP_OK
        );
    }

    /**
     * @Route("/task/{id}", name="task_update", methods={"PUT"})
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    public function update(Request $request, string $id)
    {
        /**
         * @var Task
         */
        $data = $this->em->getRepository(Task::class)->find($id);
        $taskValues = json_decode($request->getContent(), true);
        if (!$data) {
            throw $this->createNotFoundException(
                'No task found for id '.$id
            );
        }
        $data->setTitle($taskValues['title']);
        $data->setDescription($taskValues['description']);
        $this->em->flush();
        return new JsonResponse(
            ["task" => $this->serializer->normalize($data)],
            Response::HTTP_OK
        );
    }

    /**
     * @Route("/task/{id}", name="task_remove", methods={"DELETE"})
     * @param $id
     * @return JsonResponse
     * @throws ExceptionInterface
     */

    public function detele($id)
    {
        /**
         * @var Task
         */
        $data = $this->em->getRepository(Task::class)->find($id);
        if (!$data) {
            throw $this->createNotFoundException(
                'No task found for id '.$id
            );
        }
        $this->em->remove($data);
        $this->em->flush();
        return new JsonResponse(
            ["task" => $this->serializer->normalize($data)],
            Response::HTTP_OK
        );
    }
}
