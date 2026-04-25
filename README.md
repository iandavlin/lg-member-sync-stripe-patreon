# LG Member Sync

WordPress companion plugin to [`lg-stripe-billing`](https://github.com/iandavlin/lg-stripe-billing).

## What it does

- Polls Stripe Events API and Patreon OAuth on WP cron (every 5 min).
- Writes to the `lg_membership` database that the standalone Slim app also uses.
- Arbitrates per-source role opinions via `lg_role_sources`.
- **Sole writer of `wp_usermeta.wp_capabilities`** for `looth1–4` tiers — preserves admin / bbp_participant / etc.

Runs alongside the Slim app, which handles the synchronous user-facing checkout flow. Slim does immediate provisioning on Stripe redirect; this plugin catches up everything that happens later (renewals, cancellations, refunds, Patreon pledge changes).

## Phases

| Phase | Status | Purpose |
|---|---|---|
| 1 | in progress | Plugin scaffold + cron registration + arbiter/cursor tables + DB settings UI |
| 2 | planned | Stripe Events API poller — actually consumes events |
| 3 | planned | Arbiter logic + `wp_capabilities` writer |
| 4 | planned | Patreon poller (port from existing `lg-patreon-sync`) |
| 5 | planned | Cutover — retire old `lg-stripe-membership` + `lg-patreon-sync` plugins |

## Install (dev)

```bash
cd /var/www/dev/wp-content/plugins
git clone https://github.com/iandavlin/lg-member-sync.git
cd lg-member-sync
composer install --no-dev
wp plugin activate lg-member-sync
```

Then **Settings → LG Member Sync** to set the `lg_membership` DB password.

## Architecture

- DB: connects to `lg_membership` via PDO. Two new tables (`lg_role_sources`, `lg_event_cursor`) live there.
- Cron: registers `lgms_poll_tick` on a 5-minute custom interval.
- Namespace: `LGMS\` (PSR-4 under `src/`).
