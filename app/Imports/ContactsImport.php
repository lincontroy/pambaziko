<?php

namespace App\Imports;

use App\Models\Upload;
use App\Models\Contact;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ContactsImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    protected $upload;

    public function __construct(Upload $upload)
    {
        $this->upload = $upload;
    }

    public function model(array $row)
    {
        return new Contact([
            'upload_id' => $this->upload->id,
            'user_id' => $this->upload->user_id,
            'phone' => $row['phone'] ?? $row['Phone'] ?? $row['PHONE'] ?? null,
            'amount' => $row['amount'] ?? $row['Amount'] ?? $row['AMOUNT'] ?? 0,
            'status' => 'pending',
        ]);
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}