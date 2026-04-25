<?php

declare(strict_types=1);

namespace LGMS;

use LGMS\Stripe\Client as StripeClient;
use LGMS\Stripe\EventHandler as StripeEventHandler;
use LGMS\Stripe\Poller as StripePoller;
use Throwable;

/**
 * Cron entrypoint. Runs every poll interval (5 min on prod via OS cron;
 * manually triggerable on dev via `wp cron event run lgms_poll_tick`).
 *
 * Phase 2: drives the Stripe Events API poller. Patreon poller (Phase 4)
 * and arbiter sweep (Phase 3) will be added here in future passes.
 */
final class Tick
{
    public static function run(): void
    {
        $log = LGMS_PLUGIN_DIR . 'tick.log';
        $line = sprintf( "[%s] tick start\n", gmdate( 'c' ) );
        @file_put_contents( $log, $line, FILE_APPEND );

        try {
            $client  = new StripeClient();
            $handler = new StripeEventHandler( $client );
            $poller  = new StripePoller( $client, $handler );
            $result  = $poller->poll();

            $summary = sprintf(
                "[%s] stripe poll: status=%s processed=%d cursor=%s\n",
                gmdate( 'c' ),
                $result['status'],
                $result['processed'],
                $result['cursor'] ?? '(none)',
            );
            @file_put_contents( $log, $summary, FILE_APPEND );

            foreach ( $result['log'] as $entry ) {
                @file_put_contents( $log, "[{$result['cursor']}] {$entry}\n", FILE_APPEND );
            }
        } catch ( Throwable $e ) {
            $msg = sprintf( "[%s] stripe poll FAILED: %s\n", gmdate( 'c' ), $e->getMessage() );
            @file_put_contents( $log, $msg, FILE_APPEND );
        }
    }
}
