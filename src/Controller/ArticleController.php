<?php


namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Role;
use App\Entity\User;
use App\Serializer\EntitySerializer;
use App\Serializer\Normalizer\RelationNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

/**
 * Class ArticleController
 * @package App\Controller
 * @Route("/")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/", methods="GET")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(Article::class);

        return $this->render('article/list.html.twig', ['articles' => $repository->findAll()]);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/{id}", methods="GET")
     */
    public function show($id)
    {
        $this->getDoctrine()->getManager()->clear(User::class);
        $this->getDoctrine()->getManager()->clear(Role::class);
        $this->getDoctrine()->getManager()->clear(Comment::class);
        $model = $this->getDoctrine()->getRepository(User::class)->with('articles.comments.user')->find($id);
        dd($model);
    }
}

