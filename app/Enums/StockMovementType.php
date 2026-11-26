<?php

namespace App\Enums;

enum StockMovementType: string
{
    /** Stock that existed before the ledger started tracking a product. */
    case Opening = 'opening';

    case Purchase = 'purchase';

    case Sale = 'sale';

    case Return = 'return';

    /** Manual correction, e.g. after a stock count. */
    case Adjustment = 'adjustment';
}
