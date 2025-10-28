<?php

namespace App\Enums;

enum EventType: string {
    case DEPOSIT  = 'deposit';
    case WITHDRAW = 'withdraw';
    case TRANSFER = 'transfer';
}