<?php

declare(strict_types=1);

namespace LGMS\Repos;

use LGMS\Db;
use LGMS\Uuid;
use PDO;

final class EntitlementRepo
{
    public const KIND_MEMBERSHIP_TIER = 'membership_tier';

    public const SOURCE_SUBSCRIPTION = 'subscription';
    public const SOURCE_ORDER        = 'order';

    public static function activeForCustomer(int $customerId): array
    {
        $stmt = Db::pdo()->prepare(
            'SELECT * FROM entitlements
             WHERE customer_id = ?
               AND revoked_at IS NULL
               AND starts_at <= NOW()
               AND (expires_at IS NULL OR expires_at > NOW())
             ORDER BY id DESC'
        );
        $stmt->execute( [ $customerId ] );
        return $stmt->fetchAll( PDO::FETCH_ASSOC );
    }

    /**
     * Grant a membership tier from a subscription. Revokes any prior entitlement
     * sourced from the same subscription so tier changes leave a clean trail.
     */
    public static function grantMembershipFromSubscription(
        int $customerId,
        string $tierRef,
        int $subscriptionId,
    ): void {
        self::revokeBySource( self::SOURCE_SUBSCRIPTION, $subscriptionId );
        Db::pdo()->prepare(
            'INSERT INTO entitlements
                (uuid, customer_id, kind, ref, source_type, source_id)
             VALUES (?, ?, ?, ?, ?, ?)'
        )->execute( [
            Uuid::v4(),
            $customerId,
            self::KIND_MEMBERSHIP_TIER,
            $tierRef,
            self::SOURCE_SUBSCRIPTION,
            $subscriptionId,
        ] );
    }

    public static function revokeBySource(string $sourceType, int $sourceId): void
    {
        Db::pdo()->prepare(
            'UPDATE entitlements SET revoked_at = NOW()
             WHERE source_type = ? AND source_id = ? AND revoked_at IS NULL'
        )->execute( [ $sourceType, $sourceId ] );
    }

    /** Currently active membership tier ref for a customer, or null. */
    public static function activeTier(int $customerId): ?string
    {
        $rows  = self::activeForCustomer( $customerId );
        $tiers = array_values( array_filter(
            $rows,
            static fn (array $r): bool => $r['kind'] === self::KIND_MEMBERSHIP_TIER,
        ) );
        if ( $tiers === [] ) {
            return null;
        }
        usort( $tiers, static fn (array $a, array $b): int => strcmp( $b['ref'], $a['ref'] ) );
        return (string) $tiers[0]['ref'];
    }
}
