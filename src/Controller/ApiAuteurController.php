<?php

namespace App\Controller;

use App\Entity\Auteur;
use App\Repository\AuteurRepository;
use App\Repository\NationaliteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiAuteurController extends AbstractController
{
    /**
     * @Route("/api/auteurs", name="api_auteurs_list", methods={"GET"})
     */
    public function index(AuteurRepository $repo, SerializerInterface $serializer)
    {

        $auteurs = $repo->findAll();
        $resultats = $serializer->serialize($auteurs, 'json', [
            'groups' => ['listAuteurFull']
        ]);


        return new JsonResponse($resultats, 200, [], true);
    }




    /**
     * @Route("/api/auteurs/{id}/show", name="api_auteurs_show", methods={"GET"})
     */
    public function show(Auteur $auteur, SerializerInterface $serializer)
    {

        $resultats = $serializer->serialize($auteur, 'json', [
            'groups' => ['listAuteurFull']
        ]);
        return new JsonResponse($resultats, Response::HTTP_OK, [], true);
    }





    /**
     * @Route("/api/auteurs", name="api_auteurs_create", methods={"POST"})
     */
    public function create(Request $request, EntityManagerInterface $manager, SerializerInterface $serializer, ValidatorInterface $validator, DecoderInterface $decode, NationaliteRepository $natioRepo)
    {

        $data = $request->getContent();
        $dataTab = $decode->decode($data, 'json');
        $auteur = new Auteur();

        $nationalite = $natioRepo->find($dataTab['nationalite']['id']);

        $serializer->deserialize($data, Auteur::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $auteur]);
        $auteur->setNationalite($nationalite);

        $error = $validator->validate($auteur);

        if (count($error)) {
            $errors = $serializer->serialize($error, 'json');
            return new JsonResponse($errors, Response::HTTP_BAD_REQUEST, [], true);
        }
        $manager->persist($auteur);
        $manager->flush();


        return new JsonResponse("Auteur créé", Response::HTTP_CREATED, [
            "location" => "/api/Auteurs/" . $auteur->getId() . "/show"
        ], true);
    }

    /**
     * @Route("/api/auteurs/{id}/update", name="api_auteurs_update", methods={"PUT"})
     */
    public function update(Auteur $auteur, Request $request, SerializerInterface $serializer, EntityManagerInterface $manager, ValidatorInterface $validator, DecoderInterface $decode, NationaliteRepository $natioRepo)
    {
        $data = $request->getContent();
        $dataTab = $decode->decode($data, 'json');
        $nationalite = $natioRepo->find($dataTab['nationalite']['id']);

        $serializer->deserialize($data, Auteur::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $auteur]);
        $auteur->setNationalite($nationalite);

        $error = $validator->validate($auteur);
        if (count($error)) {
            $errors = $serializer->serialize($error, 'json');
            return new JsonResponse($errors, Response::HTTP_BAD_REQUEST, [], true);
        }
        $manager->persist($auteur);
        $manager->flush();
        return new JsonResponse("l'auteur modifié", Response::HTTP_OK, [], true);
    }



    /**
     * @Route("/api/auteurs/{id}/delete", name="api_auteurs_delete", methods={"DELETE"})
     */
    public function delete(Auteur $auteur, Request $request, SerializerInterface $serializer, EntityManagerInterface $manager)
    {
        $manager->remove($auteur);
        $manager->flush();
        return new JsonResponse("l'auteur a bien été supprimé", Response::HTTP_OK, []);
    }
}
