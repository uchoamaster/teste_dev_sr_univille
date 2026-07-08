<?php

namespace App\Domain\Transactions\Enums;

enum TransactionStatus: string
{
    case Pending = 'pending';
    case Processed = 'processed';
    case Failed = 'failed';
    case Invalid = 'invalid';
}