<?php

declare(strict_types=1);

namespace LGMS;

/**
 * Cron entrypoint. Runs every poll interval.
 *
 * Phase 1: just logs "tick" so we can verify cron is firing.
 * Phase 2: invoke StripePoller, PatreonPoller, then Arbiter sweep.
 */
final class Tick
{
    public static function run(): void
    {
        $log = LGMS_PLUGIN_DIR . 'tick.log';
        $msg = sprintf( "[%s] tick\n", gmdate( 'c' ) );
        @file_put_contents( $log, $msg, FILE_APPEND );

        // Phase 2 will replace this with:
        //   ( new Stripe\Poller( Db::pdo() ) )->poll();
        //   ( new Patreon\Poller( Db::pdo() ) )->poll();
        //   ( new Arbiter( Db::pdo() ) )->syncAll();
    }
}
