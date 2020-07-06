<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiGenreController extends AbstractController
{
    /**
     * @Route("/api/genres", name="api_genres_list", methods={"GET"})
     */
    public function index(GenreRepository $repo, SerializerInterface $serializer)
    {

        $genres = $repo->findAll();
        $resultats = $serializer->serialize($genres, 'json', [
            'groups' => ['listGenreFull']
        ]);


        return new JsonResponse($resultats, 200, [], true);
    }




    /**
     * @Route("/api/genres/{id}/show", name="api_genres_show", methods={"GET"})
     */
    public function show(Genre $genre, SerializerInterface $serializer)
    {

        $resultats = $serializer->serialize($genre, 'json', [
            'groups' => ['listGenreSimple']
        ]);

        return new JsonResponse($resultats, Response::HTTP_OK, [], true);
    }





    /**
     * @Route("/api/genres", name="api_genres_create", methods={"POST"})
     */
    public function create(Request $request, EntityManagerInterface $manager, SerializerInterface $serializer, ValidatorInterface $validator)
    {

        $data = $request->getContent();
        $genre = $serializer->deserialize($data, Genre::class, 'json');
        $error = $validator->validate($genre);

        if (count($error)) {
            $errors = $serializer->serialize($error, 'json');
            return new JsonResponse($errors, Response::HTTP_BAD_REQUEST, [], true);
        }
            $manager->persist($genre);
            $manager->flush();
        

        return new JsonResponse("genre créé", Response::HTTP_CREATED, [
            "location" => "/api/genres/" . $genre->getId() . "/show"
        ], true);
    }

    /**
     * @Route("/api/genres/{id}/update", name="api_genres_update", methods={"PUT"})
     */
    public function update(Genre $genre, Request $request, SerializerInterface $serializer, EntityManagerInterface $manager, ValidatorInterface $validator)
    {
        $data = $request->getContent();
        $serializer->deserialize($data, genre::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $genre]);
        $error = $validator->validate($genre);
        if (count($error)) {
            $errors = $serializer->serialize($error, 'json');
            return new JsonResponse($errors, Response::HTTP_BAD_REQUEST, [], true);
        }
            $manager->persist($genre);
            $manager->flush();
        

        return new JsonResponse("le genre a bien été modifié", Response::HTTP_OK, [], true);
    }


    /**
     * @Route("/api/genres/{id}/delete", name="api_genres_delete", methods={"DELETE"})
     */
    public function delete(Genre $genre, Request $request, SerializerInterface $serializer, EntityManagerInterface $manager)
    {
        $manager->remove($genre);
        $manager->flush();
        return new JsonResponse("le genre a bien été supprimé", Response::HTTP_OK, []);
    }
}
