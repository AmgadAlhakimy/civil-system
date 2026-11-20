<?php

namespace App\Jobs;

use App\Models\ServiceRequest;
use App\Models\User;
use App\Services\DocumentVerificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessStoredDocumentVerification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 180;

    public int $tries = 1;

    public function __construct(
        public string $serviceRequestId,
        public string $documentReference,
        public ?string $performedById = null,
    ) {
        $this->onQueue('document-verifications');
    }

    public function handle(DocumentVerificationService $service): void
    {
        $record = ServiceRequest::query()->findOrFail($this->serviceRequestId);

        $performedBy = $this->performedById
            ? User::query()->find($this->performedById)
            : null;

        $service->verifyStoredDocument(
            $record,
            $this->documentReference,
            $performedBy,
        );
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Document verification queue job failed.', [
            'service_request_id' => $this->serviceRequestId,
            'document_reference' => $this->documentReference,
            'performed_by_id' => $this->performedById,
            'exception' => $exception->getMessage(),
        ]);
    }
}
