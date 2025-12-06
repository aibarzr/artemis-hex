<?php

namespace App\Http\Controllers;

use App\Domain\Evaluator\Ports\EvaluatorRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class EvaluatorController extends Controller
{
    public function __construct(
        private readonly EvaluatorRepositoryInterface $evaluatorRepository,
    ) {}

    public function index(): JsonResponse
    {
        try {
            $evaluators = $this->evaluatorRepository->getAll();

            $data = array_map(function ($evaluator) {
                return [
                    'id' => $evaluator->id(),
                    'name' => $evaluator->name(),
                    'email' => $evaluator->email()->value(),
                    'specialization' => $evaluator->specialization()->value(),
                    'max_applications' => $evaluator->maxApplications(),
                    'current_applications_count' => $evaluator->currentApplicationsCount(),
                    'is_active' => $evaluator->isActive(),
                    'can_evaluate' => $evaluator->canEvaluate(),
                ];
            }, $evaluators);

            return response()->json([
                'message' => 'Evaluators retrieved successfully.',
                'data' => $data,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while retrieving evaluators.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $evaluator = $this->evaluatorRepository->findById($id);

            if ($evaluator === null) {
                return response()->json([
                    'message' => 'Evaluator not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'message' => 'Evaluator retrieved successfully.',
                'data' => [
                    'id' => $evaluator->id(),
                    'name' => $evaluator->name(),
                    'email' => $evaluator->email()->value(),
                    'specialization' => $evaluator->specialization()->value(),
                    'max_applications' => $evaluator->maxApplications(),
                    'current_applications_count' => $evaluator->currentApplicationsCount(),
                    'is_active' => $evaluator->isActive(),
                    'can_evaluate' => $evaluator->canEvaluate(),
                    'has_reached_max' => $evaluator->hasReachedMaxApplications(),
                ],
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while retrieving the evaluator.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
