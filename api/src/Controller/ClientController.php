<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Recommendation;
use App\Repository\ClientRepository;
use App\Repository\RecommendationRepository;
use App\Result\ClientSummaryResult;
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
        $name = $request->query->get('name');
        $orderBy = $request->query->get('order', []);

        $clients = $this->clientRepository->findManyBy($name, $orderBy, $limit, $offset);
        $clientsIds = array_map(fn(Client $client) => $client->getId(), $clients);

        $recommendations = $this->recommendationRepository->findBy(['client' => $clientsIds], ['id' => 'DESC']);

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

    /**
     * @Route("/clients/{id}/recommendations", name="recommendations", methods={"GET"})
     * @param int $id
     *
     * @return Response
     */
    public function recommendations(int $id): Response
    {
        $client = $this->clientRepository->find($id);

        if (!$client instanceof Client) {
            return new Response(sprintf('Unknown client with id "%d"', $id), Response::HTTP_NOT_FOUND);
        }

        $recommendations = $this->recommendationRepository->findBy(['client' => $id]);

        return $this->json([
            'data' => $recommendations,
        ]);
    }
}
