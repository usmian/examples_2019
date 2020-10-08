<?php
/**
 * Created by PhpStorm.
 * UserController: Усиков
 * Date: 08.12.2018
 * Time: 23:10
 */

namespace App\Controller;

use App\Repository\RoleRepository;
use App\Repository\ModuleRepository;
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

class ApiSecurityController extends AbstractController
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
     * @Route("/api/init_app", name="init_app"))
     */
    public function initAction(ModuleRepository $modulesRepo)
    {
        $user = $this->getUser();

        $access = $modulesRepo->findWithRoles();
        $grantedList = [];
        $userRoles = $user->getRoles();
        if (in_array('ROLE_ADMIN', $userRoles)) {
            foreach ($access as $a) {
                array_push($grantedList, $a['name']);
            }
        } else {
            foreach ($access as $a) {
                $roles = $a['roles'];
                foreach ($roles as $r) {
                    if (in_array($r['name'], $userRoles)) {
                        array_push($grantedList, $a['name']);
                    }
                }
            }
        }
        $user = $this->serializer->serialize($user, 'json');

        $grantedList = array_unique($grantedList);
        $response = [
            'grantedList' => $grantedList,
            'access' => $access,
            'UserController' => $user
        ];

        $response = $this->serializer->serialize($response, 'json');
        return new JsonResponse($response, 200, [], true);
    }

    /**
     * @Route("/api/security/get_access_modules", name="get_access_modules"))
     */
    public function getAccessAction(RoleRepository $rolesRepo,
                                    ModuleRepository $modulesRepo)
    {
        $roles = $rolesRepo->findAll();
        $access = $modulesRepo->findAll();

        $response = [
            'roles' => $roles,
            'access' => $access
        ];

        $response = $this->serializer->serialize($response, 'json');

        return new JsonResponse($response, 200, [], true);
    }

    /**
     * @Route("/api/security/switch_role", name="switch_role"))
     */
    public function switchRoleAction(ModuleRepository $modulesRepo,
                                     Request $request,
                                     EntityManagerInterface $entityManager)
    {
        $moduleID = $request->request->get('payload')['module_id'];
        $roleID = $request->request->get('payload')['id'];

        $module = $modulesRepo->find($moduleID);

        $set = true;
        foreach ($module->getRoles() as $role) {
            if ($role->getId() == $roleID) {
                $set = false;
            }
        };

        if ($set) {
            $module->addRole($entityManager->getReference('App\Entity\Role', $roleID));
        } else {
            $module->removeRole($entityManager->getReference('App\Entity\Role', $roleID));
        }

        $entityManager->persist($module);
        $entityManager->flush();

        $response = [
            'module' => $module
        ];

        $response = $this->serializer->serialize($response, 'json');
        return new JsonResponse($response, 200, [], true);
    }
}