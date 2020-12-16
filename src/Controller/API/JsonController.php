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
        if ($this->query['with'])
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
        if ($this->query['with'])
            $model = $model->with(...$this->query['with']);
        $model = $model->find($id);

        return new Response(
            $this->serializer->encode($model),
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
     * @Route("/api/{entity}/{id}", methods={"PUT"}, name="api.update")
     */
    public function update(string $id): Response
    {
        $model = $this->getDoctrine()->getRepository($this->entityClass)->find($id);
        $data = $this->serializer->decodeInto($this->request->getContent(), $model);
//        $data = $serializer_factory->decode($this->request->getContent(), $this->entityClass);

        dd($data);


        return new Response(
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
    }
}
