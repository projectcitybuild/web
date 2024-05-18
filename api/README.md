# API

Laravel-powered web backend

## Common Tasks

### Dev Container

Boot up dev containers for local development

```
sail up -d
```

### Payments

Stripe CLI is integrated into our `docker-compose.yml` so that webhook events can be forwarded from Stripe to the local dev container.

1. Make sure `.env` has values for `STRIPE_KEY`, `STRIPE_SECRET` and `STRIPE_CLI_DEVICE_NAME`
2. After starting the dev containers, run `docker-compose logs -f stripe-cli` to watch Stripe CLI logs. On first run, grab the webhook secret output by the logs, and put that as your `STRIPE_WEBHOOK_SECRET` in `.env`. Without this, webhook signature validation will always fail.

From this point on, any Stripe checkout events will be forwarded to the local dev container (eg. to process payments, etc)
