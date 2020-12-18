<?php

namespace App\Controller\API;

use \App\Service\Serializer\EntitySerializer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JsonController extends AbstractController
{
    protected $serializer;
    protected $request;
    protected $query;
    protected ?string $entityName;
    protected ?string $entityClass;
    protected ?string $route;
    protected ?string $path;

    /**
     * __construct
     *
     * @param EntitySerializer $serializer
     * @param RequestStack $requestStack injected RequestStack
     */
    public function __construct(EntitySerializer $serializer, RequestStack $requestStack)
    {
        $this->serializer = $serializer;
        $this->request = $requestStack->getCurrentRequest();
        $this->query = $this->request->query->all();
        $this->route = $this->request->attributes->get("_route");
        $this->params = $this->request->attributes->get("_route_params");
        if (!isset($this->entityName)) $this->entityName = ucwords($this->request->attributes->get("entity"));
        if (!isset($this->entityClass)) $this->entityClass = "App\Entity\\" . $this->entityName;
        if (!isset($this->path)) $this->path = lcfirst($this->entityName);
    }

    /**
     * Get a list of resources
     *
     * @return Response
     *
     * @Route("/api/{entity}", methods={"GET"}, name="api.index")
     */
    public function index(): Response
    {
        $this->getDoctrine()->getManager()->clear($this->entityClass);
        $models = $this->getDoctrine()->getRepository($this->entityClass);
        if (isset($this->query['with']))
            $models = $models->with(...$this->query['with']);
        $models = $models->findAll();

        return new Response(
            $this->serializer->encode($models),
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
    }

    /**
     * Get a resource
     *
     * @param string $id
     * @return Response
     *
     * @Route("/api/{entity}/{id}", methods={"GET"}, name="api.show")
     */
    public function show(string $id): Response
    {
        $model = $this->getDoctrine()->getRepository($this->entityClass);
        if (isset($this->query['with']))
            $model = $model->with(...$this->query['with']);
        $model = $model->find($id);

        return new Response(
            $this->serializer->encode($model),
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
    }

    /**
     * Update a resource
     *
     * @param string $id
     * @return Response
     *
     * @Route("/api/{entity}/{id}", methods={"PUT"}, name="api.update")
     */
    public function update(string $id): Response
    {
        $model = $this->getDoctrine()->getRepository($this->entityClass)->find($id);
        $data = $this->serializer->decodeInto($this->request->getContent(), $model);
//        $data = $serializer_factory->decode($this->request->getContent(), $this->entityClass);


        return new Response(
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
    }

    /**
     * Test updating a resource
     *
     * @param string $id
     * @return Response
     *
     * @Route("/api/{entity}/{id}/put", methods={"GET"}, name="api.update.test")
     */
    public function updateTest(string $id): Response
    {
        $body = '{"title":"Autem perferendis ducimus id","coverPhotoUrl":"https:\/\/picsum.photos\/seed\/SxgdDXZy\/900\/450","body":"Autem perferendis ducimus id. Non est autem magni nobis aut odit rem. Odit ut ratione nihil sint consectetur. Dolores hic possimus voluptas voluptatem.\n\nQuisquam ut officia aut itaque. Minima laboriosam atque omnis exercitationem repudiandae aut.\n\nVoluptates voluptas impedit sed temporibus et. Doloribus veritatis voluptatem dolores qui eos et est sunt. Officiis et assumenda exercitationem accusantium tempora rem. Alias magni et voluptatem nihil voluptatem dolor.","id":1,"createdAt":"2020-07-31T12:04:08+00:00","updatedAt":"2020-07-31T12:04:08+00:00","author":{"id":1,"name":"Robyn Kiehn Jr.","email":"pwaelchi@example.net","emailVerifiedAt":"2020-10-19T14:09:22+00:00","password":"$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC\/.og\/at2.uheWG\/igi","twoFactorSecret":null,"twoFactorRecoveryCodes":null,"rememberToken":"cMwuWhoDP5","profilePhotoPath":null,"createdAt":"2020-10-19T14:09:22+00:00","updatedAt":"2020-10-19T14:09:22+00:00"},"slug":"gabbard-2020","label":"Gabbard 2020"}';

        $model = $this->getDoctrine()->getRepository($this->entityClass)->find($id);
        $model = $this->serializer->decodeInto($body, $model);

        $uow = $this->getDoctrine()->getManager()->getUnitOfWork();
        $uow->computeChangeSets();


        $this->getDoctrine()->getManager()->flush($model);
        return $this->render('dump.html.twig', ['vars' => $model]);
    }
}
