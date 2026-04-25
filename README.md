# LG Member Sync — DEPRECATED 2026-04-25

> ⚠️ **This plugin has been retired.** Its entire codebase was folded into
> [`lg-patreon-stripe-poller`](https://github.com/iandavlin/lg-patreon-stripe-poller)
> (formerly `lg-patreon-oauth`) on **2026-04-25**.
>
> The merged plugin now owns:
> - Patreon OAuth onboarding (the original `lg-patreon-onboard` half)
> - Patreon API polling
> - Stripe Events API polling (this repo's contribution)
> - WP user provisioning
> - `lg_role_sources` arbitration
> - `wp_capabilities` writes
>
> Slim API companion: [`lg-stripe-billing`](https://github.com/iandavlin/lg-stripe-billing).
>
> **Do not deploy or modify this repo.** All future work happens in
> `lg-patreon-stripe-poller`.

---

## Original description (historical)

WordPress companion plugin to [`lg-stripe-billing`](https://github.com/iandavlin/lg-stripe-billing). Polls Stripe + Patreon, writes to `lg_membership`, arbitrates per-source role opinions via `lg_role_sources`, and is the sole writer of `wp_usermeta.wp_capabilities` for `looth1`–`looth4` tiers.

The phased build (scaffold → Stripe poller → arbiter → Patreon poller → cutover) was completed in a single day and immediately consolidated into the unified plugin.
