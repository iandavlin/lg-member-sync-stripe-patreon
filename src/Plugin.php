<?php

declare(strict_types=1);

namespace LGMS;

/**
 * Plugin lifecycle + boot. Thin coordinator — real work is in subsystems.
 */
final class Plugin
{
    public const CRON_HOOK     = 'lgms_poll_tick';
    public const CRON_SCHEDULE = 'lgms_every_5min';

    public static function activate(): void
    {
        Schema::apply();

        if ( ! wp_next_scheduled( self::CRON_HOOK ) ) {
            wp_schedule_event( time() + 60, self::CRON_SCHEDULE, self::CRON_HOOK );
        }
    }

    public static function deactivate(): void
    {
        $ts = wp_next_scheduled( self::CRON_HOOK );
        if ( $ts ) {
            wp_unschedule_event( $ts, self::CRON_HOOK );
        }
        wp_clear_scheduled_hook( self::CRON_HOOK );
    }

    public static function boot(): void
    {
        // Custom cron interval.
        add_filter( 'cron_schedules', static function ( array $schedules ): array {
            $schedules[ self::CRON_SCHEDULE ] = [
                'interval' => 5 * MINUTE_IN_SECONDS,
                'display'  => __( 'Every 5 minutes (LGMS)', 'lg-member-sync' ),
            ];
            return $schedules;
        });

        // Cron handler — runs all pollers + arbiter sweep.
        add_action( self::CRON_HOOK, [ Tick::class, 'run' ] );

        // Admin screens (settings, status).
        if ( is_admin() ) {
            Admin::boot();
        }
    }
}
