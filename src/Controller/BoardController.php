<?php

namespace App\Controller;

use App\Entity\Board;
use App\Normalizers\Normalizer;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class BoardController extends AbstractController
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
     * @Route("/board", name="board_create", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    public function create(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $board = new Board(
            $data['title'],
            $data['category']
        );

        $this->em->persist($board);
        $this->em->flush();

        return new JsonResponse(
            ["task" => $this->serializer->normalize($data)],
            Response::HTTP_CREATED
        );
    }

    /**
     * @Route("/boards", name="board_show_all", methods={"GET"})
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    public function fetchAll()
    {
        $data = $this->em->getRepository(Board::class)->findAll();
        return new JsonResponse(
            ["tasks" => $this->serializer->normalize($data)],
            Response::HTTP_OK
        );
    }

    /**
     * @Route("/board/{id}", name="board_show_one", methods={"GET"})
     * @param string $id
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    public function fetch(string $id)
    {
        $data = $this->em->getRepository(Board::class)->find($id);

        if (!$data) {
            throw $this->createNotFoundException(
                'No board found for id '.$id
            );
        }
        return new JsonResponse(
            ["board" => $this->serializer->normalize($data)],
            Response::HTTP_OK
        );
    }

    /**
     * @Route("/board/{id}", name="board_update", methods={"PUT"})
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    public function update(Request $request, string $id)
    {
        /**
         * @var Board
         */
        $data = $this->em->getRepository(Board::class)->find($id);
        $boardValues = json_decode($request->getContent(), true);
        if (!$data) {
            throw $this->createNotFoundException(
                'No board found for id '.$id
            );
        }
        $data->setTitle($boardValues['title']);
        $data->setCategory($boardValues['category']);
        $this->em->flush();
        return new JsonResponse(
            ["board" => $this->serializer->normalize($data)],
            Response::HTTP_OK
        );
    }

    /**
     * @Route("/board/{id}", name="board_remove", methods={"DELETE"})
     * @param $id
     * @return JsonResponse
     * @throws ExceptionInterface
     */

    public function detele($id)
    {
        /**
         * @var Board
         */
        $data = $this->em->getRepository(Board::class)->find($id);
        if (!$data) {
            throw $this->createNotFoundException(
                'No board found for id '.$id
            );
        }
        $this->em->remove($data);
        $this->em->flush();
        return new JsonResponse(
            ["board" => $this->serializer->normalize($data)],
            Response::HTTP_OK
        );
    }
}
