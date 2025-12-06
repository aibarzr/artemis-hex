<?php

namespace App\Http\Controllers;

use App\Domain\Application\UseCases\AssignEvaluatorUseCase;
use App\Domain\Application\UseCases\GetApplicationSummaryUseCase;
use App\Domain\Application\UseCases\GetConsolidatedListUseCase;
use App\Domain\Application\UseCases\RegisterApplicationUseCase;
use App\Domain\Application\UseCases\ValidateApplicationUseCase;
use App\Http\Requests\AssignEvaluatorRequest;
use App\Http\Requests\ConsolidatedListRequest;
use App\Http\Requests\StoreApplicationRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApplicationController extends Controller
{
    public function __construct(
        private readonly RegisterApplicationUseCase $registerApplicationUseCase,
        private readonly ValidateApplicationUseCase $validateApplicationUseCase,
        private readonly AssignEvaluatorUseCase $assignEvaluatorUseCase,
        private readonly GetConsolidatedListUseCase $getConsolidatedListUseCase,
        private readonly GetApplicationSummaryUseCase $getApplicationSummaryUseCase,
    ) {}

    public function store(StoreApplicationRequest $request): JsonResponse
    {
        try {
            $application = $this->registerApplicationUseCase->execute(
                candidateName: $request->input('candidate_name'),
                candidateEmail: $request->input('candidate_email'),
                position: $request->input('position'),
                yearsOfExperience: $request->input('years_of_experience'),
                cvPath: $request->input('cv_path'),
                coverLetter: $request->input('cover_letter'),
            );

            return response()->json([
                'message' => 'Application registered successfully.',
                'data' => [
                    'id' => $application->id(),
                    'candidate_name' => $application->candidateName(),
                    'candidate_email' => $application->candidateEmail()->value(),
                    'position' => $application->position(),
                    'years_of_experience' => $application->yearsOfExperience()->value(),
                    'status' => $application->status()->value(),
                    'submitted_at' => $application->submittedAt()->format('Y-m-d H:i:s'),
                ],
            ], Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => 'Validation error.',
                'error' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while registering the application.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function validate(int $id): JsonResponse
    {
        try {
            $validationResult = $this->validateApplicationUseCase->execute($id);

            return response()->json([
                'message' => 'Application validated.',
                'data' => [
                    'is_valid' => $validationResult->isValid(),
                    'errors' => $validationResult->errors(),
                    'errors_count' => $validationResult->errorsCount(),
                ],
            ], Response::HTTP_OK);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => 'Application not found.',
                'error' => $e->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while validating the application.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function assignEvaluator(int $id, AssignEvaluatorRequest $request): JsonResponse
    {
        try {
            $application = $this->assignEvaluatorUseCase->execute(
                applicationId: $id,
                evaluatorId: $request->input('evaluator_id'),
            );

            return response()->json([
                'message' => 'Evaluator assigned successfully.',
                'data' => [
                    'id' => $application->id(),
                    'candidate_name' => $application->candidateName(),
                    'evaluator_id' => $application->evaluatorId(),
                ],
            ], Response::HTTP_OK);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => 'Error assigning evaluator.',
                'error' => $e->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while assigning the evaluator.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function consolidated(ConsolidatedListRequest $request): JsonResponse
    {
        try {
            $filters = [];

            if ($request->has('filter_evaluator_id')) {
                $filters['evaluator_id'] = $request->input('filter_evaluator_id');
            }

            if ($request->has('filter_min_experience')) {
                $filters['min_experience'] = $request->input('filter_min_experience');
            }

            if ($request->has('filter_max_experience')) {
                $filters['max_experience'] = $request->input('filter_max_experience');
            }

            $result = $this->getConsolidatedListUseCase->execute(
                filters: $filters,
                orderBy: $request->input('order_by', 'years_of_experience'),
                orderDirection: $request->input('order_direction', 'desc'),
                perPage: $request->input('per_page', 15),
                page: $request->input('page', 1),
            );

            return response()->json($result->toArray(), Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while retrieving the consolidated list.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function summary(int $id): JsonResponse
    {
        try {
            $summary = $this->getApplicationSummaryUseCase->execute($id);

            return response()->json([
                'message' => 'Application summary retrieved successfully.',
                'data' => $summary->toArray(),
            ], Response::HTTP_OK);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => 'Application not found.',
                'error' => $e->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while retrieving the application summary.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
