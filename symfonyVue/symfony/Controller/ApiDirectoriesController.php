<?php
/**
 * Created by PhpStorm.
 * User: Усиков
 * Date: 13.04.2019
 * Time: 10:45
 */

namespace App\Controller;

use App\Entity\TagCommon;
use App\Repository\TagCommonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ApiDirectoriesController
{
    /** @var SerializerInterface */
    private $serializer;
    private $normalizer;
    private $router;

    public function __construct(SerializerInterface $serializer, NormalizerInterface $normalizer, RouterInterface $router)
    {
        $this->serializer = $serializer;
        $this->normalizer = $normalizer;
        $this->router = $router;
    }
    /**
     * @Route("/api/get_directories", name="ajax_get_directories"))
     */
    public function get_directories(TagCommonRepository $tagsRepository)
    {
        $goods = $tagsRepository->findByType(['isArchive' => 0, 'type' => 1]);
        $regions = $tagsRepository->findByType(['isArchive' => 0, 'type' => 2]);
        $types = $tagsRepository->findByType(['isArchive' => 0, 'type' => 3]);
        /* $regions = $rRepo->findBy(['is_archive'=>0]);*/

        $all = ['goods' => $goods,
            'types' => $types,
            'regions' => $regions];

        $data = $this->serializer->serialize($all, 'json');
        return new JsonResponse($data, 200, [], true);
    }

    /**
     * @Route("/api/add_directory", name="ajax_add_directory"))
     */
    public function add_directory(Request $request)
    {
        $type = $request->request->get('type');
        $name = $request->request->get('value');

        $directory = new TagCommon();

        $directory->setName($name);
        $directory->setIsArchive(0);
        $directory->setType($type);
        $directory->setTypeName(TagCommon::$mapTypes[$type]);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($directory);
        try {
            $entityManager->flush();
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $dir = [
            'type' => $type,
            'directory' => $directory
        ];

        $data = $this->serializer->serialize($dir, 'json');
        return new JsonResponse($data, 200, [], true);
    }

    /**
     * @Route("/api/remove_directory", name="ajax_remove_directory"))
     */
    public function remove_directory(Request $request,
                                     TagCommonRepository $tagsRepository)
    {
        $type = $request->request->get('type');
        $id = $request->request->get('id');

        $entityManager = $this->getDoctrine()->getManager();
        $directory = $tagsRepository->find($id);
        $directory->setIsArchive(1);

        $entityManager->persist($directory);

        try {
            $entityManager->flush();
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return new JsonResponse(['type' => $type, 'id' => $id], 200, []);
    }

    /**
     * @Route("/api/edit_directory", name="ajax_edit_directory"))
     */
    public function edit_directory(Request $request,
                                   TagCommonRepository $tagsRepository)
    {
        $type = $request->request->get('type');
        $id = $request->request->get('id');
        $name = $request->request->get('value');

        $entityManager = $this->getDoctrine()->getManager();

        $directory = $tagsRepository->find($id);
        $directory->setName($name);

        $entityManager->persist($directory);

        try {
            $entityManager->flush();
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return new JsonResponse(['type' => $type, 'id' => $id], 200, []);
    }
}