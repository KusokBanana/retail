<?php

namespace App\Controller;

use App\Entity\Recommendation;
use App\Entity\RecommendationStatuses;
use App\Repository\RecommendationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class RecommendationController extends AbstractController
{
    private RecommendationRepository $recommendationRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        RecommendationRepository $recommendationRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->recommendationRepository = $recommendationRepository;
        $this->entityManager    = $entityManager;
    }

    /**
     * @Route("/recommendations/{id}", name="update_recommendations", methods={"PATCH"})
     */
    public function index(Request $request, int $id): Response
    {
        $recommendation = $this->recommendationRepository->find($id);

        if (!$recommendation instanceof Recommendation) {
            return new Response(sprintf('Unknown recommendation with id "%d"', $id), Response::HTTP_NOT_FOUND);
        }

        $data = $this->getRequestData($request, ['status'], ['status']);
        $status = $data->get('status');

        if (!in_array($status, RecommendationStatuses::STATUSES)) {
            throw new BadRequestHttpException(
                sprintf(
                    'Unexpected status "%s". Allowed statuses: %s',
                    $status,
                    join(', ', RecommendationStatuses::STATUSES)
                )
            );
        }

        $recommendation->update($status);
        $this->entityManager->flush();
        $this->entityManager->refresh($recommendation);

        return $this->json($recommendation);
    }
}
