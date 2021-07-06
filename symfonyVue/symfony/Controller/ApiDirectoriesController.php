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
     * Список
     *
     * @param TagCommonRepository $tagsRepository
     * @Route("/api/get_directories", name="ajax_get_tags"))
     * @return JsonResponse
     */
    public function getTags(TagCommonRepository $tagsRepository)
    {
        $goods = $tagsRepository->findByType(['isArchive' => false, 'type' => TagCommon::TYPE_GOODS]);
        $regions = $tagsRepository->findByType(['isArchive' => false, 'type' => TagCommon::TYPE_REGIONS]);
        $types = $tagsRepository->findByType(['isArchive' => false, 'type' => TagCommon::TYPE_DELIVERY]);

        $data = $this->serializer->serialize([
            'goods' => $goods,
            'types' => $types,
            'regions' => $regions
        ], 'json');
        return new JsonResponse($data, 200, [], true);
    }

    /**
     * @Route("/api/add_tag", name="ajax_add_tag"))
     */
    public function addTag(Request $request)
    {
        $type = $request->request->get('type');
        $name = $request->request->get('value');

        $newTag = new TagCommon();

        // todo запихать в менеджер
        $newTag->setName($name);
        $newTag->setIsArchive(false);
        $newTag->setType($type);
        $newTag->setTypeName(TagCommon::$mapTypes[$type]);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($newTag);
        try {
            $entityManager->flush();
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $tag = [
            'type' => $type,
            'tag' => $newTag
        ];

        $data = $this->serializer->serialize($tag, 'json');
        return new JsonResponse($data, 200, [], true);
    }

    /**
     * @Route("/api/remove_tag", name="ajax_remove_tag"))
     */
    public function removeTag(Request $request, TagCommonRepository $tagsRepository)
    {
        $type = $request->request->get('type');
        $id = $request->request->get('id');

        $entityManager = $this->getDoctrine()->getManager();
        $directory = $tagsRepository->find($id);
        $directory->setIsArchive(true);

        $entityManager->persist($directory);

        try {
            $entityManager->flush();
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return new JsonResponse(['type' => $type, 'id' => $id], 200, []);
    }

    /**
     * @Route("/api/edit_tag", name="ajax_edit_tag"))
     */
    public function editTag(Request $request, TagCommonRepository $tagsRepository)
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
