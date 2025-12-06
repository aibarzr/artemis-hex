<?php

namespace App\Http\Controllers;

use App\Domain\Reporting\UseCases\GenerateExcelReportUseCase;
use App\Http\Requests\ConsolidatedListRequest;
use App\Jobs\GenerateExcelReportJob;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    public function __construct(
        private readonly GenerateExcelReportUseCase $generateExcelReportUseCase,
    ) {}

    public function generateExcel(ConsolidatedListRequest $request): JsonResponse
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

            $notificationEmail = $request->input('notification_email', '');

            GenerateExcelReportJob::dispatch(
                filters: $filters,
                orderBy: $request->input('order_by', 'years_of_experience'),
                orderDirection: $request->input('order_direction', 'desc'),
                notificationEmail: $notificationEmail,
            );

            return response()->json([
                'message' => 'Excel report generation has been queued successfully.',
                'data' => [
                    'status' => 'queued',
                    'queue' => 'reports',
                    'notification_email' => $notificationEmail ?: null,
                ],
            ], Response::HTTP_ACCEPTED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while queueing the Excel report.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
