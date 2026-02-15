<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InvoiceNumberService
{
    public function __construct(
        private readonly SettingsService $settings
    ) {}

    public function next(?Carbon $date = null): string
    {
        $date ??= now();

        return DB::transaction(function () use ($date) {

            $format = $this->ensureAndLockGet('invoice.format', 'INV-{YYYY}-{000000}');
            $reset  = $this->ensureAndLockGet('invoice.numbering.reset', 'yearly');
            $next   = (int) $this->ensureAndLockGet('invoice.next_number', '1');
            $seqYear = (int) $this->ensureAndLockGet('invoice.sequence_year', (string) $date->year);

            if ($reset === 'yearly' && $seqYear !== (int)$date->year) {
                $seqYear = (int)$date->year;
                $next = 1;

                $this->lockedUpdate('invoice.sequence_year', (string) $seqYear);
                $this->lockedUpdate('invoice.next_number', (string) $next);
            }

            $invoiceNo = $this->renderFormat($format, $date, $next);

            $this->lockedUpdate('invoice.next_number', (string) ($next + 1));

            $this->settings->forgetCache();

            return $invoiceNo;
        }, 3);
    }


    private function ensureAndLockGet(string $key, string $default): string
    {
        DB::table('settings')->updateOrInsert(
            ['key' => $key],
            [
                'value' => DB::raw("COALESCE(value, '{$this->escapeSql($default)}')"),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        // Now lock and read the existing row
        $row = DB::table('settings')
            ->where('key', $key)
            ->lockForUpdate()
            ->first();

        return $row?->value ?? $default;
    }

    /**
     * Update a known-existing row while we're already inside a transaction.
     * created_at is not touched.
     */
    private function lockedUpdate(string $key, ?string $value): void
    {
        DB::table('settings')
            ->where('key', $key)
            ->update([
                'value' => $value,
                'updated_at' => now(),
            ]);
    }

    private function renderFormat(string $format, Carbon $date, int $sequence): string
    {
        $out = $format;

        $out = str_replace('{YYYY}', $date->format('Y'), $out);
        $out = str_replace('{YY}',   $date->format('y'), $out);
        $out = str_replace('{MM}',   $date->format('m'), $out);
        $out = str_replace('{DD}',   $date->format('d'), $out);

        $out = preg_replace_callback('/\{(0+)\}/', function ($m) use ($sequence) {
            $len = strlen($m[1]);
            return str_pad((string) $sequence, $len, '0', STR_PAD_LEFT);
        }, $out);

        return $out;
    }

    private function escapeSql(string $value): string
    {
        return str_replace("'", "''", $value);
    }
}
