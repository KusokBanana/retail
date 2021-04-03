<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Recommendation;
use App\Repository\ClientRepository;
use App\Repository\RecommendationRepository;
use App\Result\ClientSummaryResult;
use Assert\Assert;
use Assert\InvalidArgumentException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends AbstractController
{
    private ClientRepository $clientRepository;
    private RecommendationRepository $recommendationRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        ClientRepository $clientRepository,
        RecommendationRepository $recommendationRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->clientRepository = $clientRepository;
        $this->recommendationRepository = $recommendationRepository;
        $this->entityManager    = $entityManager;
    }

    /**
     * @Route("/clients", name="clients", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        $limit = $request->query->get('limit', 50);
        $offset = $request->query->get('offset', 0);

        $clients = $this->clientRepository->findBy([], ['id' => 'DESC'], $limit, $offset);
        $clientsIds = array_map(fn(Client $client) => $client->getId(), $clients);

        $recommendations = $this->recommendationRepository->findBy(['client' => $clientsIds]);

        $result = [];
        foreach ($clients as $client) {
            $clientRecommendations = array_filter(
                $recommendations,
                fn(Recommendation $recommendation) => $recommendation->getClient() === $client
            );
            $result[] = new ClientSummaryResult($client, $clientRecommendations);
        }

        return $this->json([
            'limit' => $limit,
            'offset' => $offset,
            'total' => $this->clientRepository->getTotal(),
            'data' => $result,
        ]);
    }
//
//    /**
//     * @Route("/candidates/{id}", name="candidate", methods={"GET"})
//     * @param int $id
//     *
//     * @return Response
//     */
//    public function candidate(int $id): Response
//    {
//        $candidate = $this->candidateRepository->findOneBy(['id' => $id]);
//
//        if (!$candidate instanceof Candidate) {
//            return new Response(sprintf('Unknown candidate with id "%s"', $id), Response::HTTP_NOT_FOUND);
//        }
//
//        return $this->json([
//            'data' => $candidate,
//        ]);
//    }
//
//    /**
//     * @Route("/candidates", name="create_candidate", methods={"POST"})
//     * @param Request $request
//     *
//     * @return Response
//     */
//    public function create(Request $request): Response
//    {
//        $candidate = null;
//        try {
//            $parameters = $this->getRequestData(
//                $request,
//                ['skills', 'name', 'sex', 'city', 'birth_date', 'title', 'salary', 'education_history', 'experience', 'languages', 'about', 'status']
//            );
//
//            $this->entityManager->transactional(function() use (&$candidate, $parameters) {
//                $candidate = $this->candidateFactory->create($this->getUser(), $parameters);
//            });
//        } catch (InvalidArgumentException $exception) {
//            return new Response($exception->getMessage(), Response::HTTP_BAD_REQUEST);
//        }
//
//        $this->entityManager->refresh($candidate);
//
//        return $this->json([
//            'data' => $candidate,
//        ]);
//    }
//
//    /**
//     * @Route("/candidates/{id}", name="update_candidate", methods={"PATCH"})
//     * @param Request $request
//     * @param int     $id
//     *
//     * @return Response
//     */
//    public function update(Request $request, int $id): Response
//    {
//        $candidate = $this->candidateRepository->findOneBy(['id' => $id]);
//
//        if (!$candidate instanceof Candidate) {
//            return new Response(sprintf('Unknown candidate with id "%s"', $id), Response::HTTP_NOT_FOUND);
//        }
//
//        if ($this->getUser()->getUsername() !== $candidate->getAuthor()->getUsername()) {
//            return new Response('You are not allowed to update this candidate', Response::HTTP_FORBIDDEN);
//        }
//
//        try {
//            $parameters = $this->getRequestData(
//                $request,
//                ['skills', 'name', 'sex', 'city', 'birth_date', 'title', 'salary', 'education_history', 'experience', 'languages', 'about', 'status']
//            );
//
//            Assert::that($parameters->count())->greaterThan(0);
//
//            $this->entityManager->transactional(function() use ($candidate, $parameters) {
//                $this->candidateFactory->update($this->getUser(), $candidate, $parameters);
//            });
//        } catch (InvalidArgumentException $exception) {
//            return new Response($exception->getMessage(), Response::HTTP_BAD_REQUEST);
//        }
//
//        $this->entityManager->refresh($candidate);
//
//        return $this->json([
//            'data' => $candidate,
//        ]);
//    }
}
