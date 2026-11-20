# Discord role sync

The website links accounts, decides which roles users should have, and maps website roles to Discord roles. A seperate bot makes Discord API calls. 

## 1. Create a Passport client

Add an OAuth client via the [manage](http://localhost/manage) panel, 

Or alternatively, run this command:

```shell
php artisan passport:client --client --name="Discord"
```

The command shows the client ID and secret only one-time.

- Store both values in the bot's secret store.
- Set the client ID as `DISCORD_PASSPORT_CLIENT_ID` on the website.
- Keep the secret out of website configuration, source control, frontend code, and logs. Passport stores it as a hash.

The bot requests a token from `/oauth/token`. Use `grant_type=client_credentials` and `scope=discord.roles.sync`. The token lasts 30 minutes. Request a new token before expiry. Do not use a refresh token.

The API accepts only this configured client and the required scope. It rejects user tokens and tokens from other clients.

## 2. Configure Discord login and callback

Register this callback URI in the Discord application:

```text
/account/settings/discord/callback
```

The website requests only the Discord `identify` scope. It uses this scope to confirm account ownership and does not store the OAuth access token. Users link or unlink an account at **Account → Manage Discord account**.

Set these values in the deployment secret or configuration store:

| Variable | Used by | Purpose |
| --- | --- | --- |
| `DISCORD_CLIENT_ID` | Website | Discord OAuth application ID |
| `DISCORD_CLIENT_SECRET` | Website | Secret used to exchange the OAuth code |
| `DISCORD_REDIRECT_URI` | Website | Exact registered callback URL |
| `DISCORD_GUILD_ID` | Website and bot | The one guild (a.k.a. server id) managed by this integration |
| `DISCORD_PASSPORT_CLIENT_ID` | Website | Allowed Passport client ID |
| `DISCORD_BOT_CALLBACK_URL` | Website | HTTPS bot URL that starts a role sync |
| `DISCORD_CALLBACK_SECRET` | Website and bot | Shared secret for callback signatures |

Store the Discord bot token only in the bot's secret store. The website does not need it.

## 3. Map roles and set bot permissions

1. Set `DISCORD_GUILD_ID`.
2. Open **Manage → Discord Role Mappings**.
3. Add a mapping for each website role and Discord role.
4. Give the bot **Manage Roles** permission. Put the bot's highest role above every mapped role.

Disabled mappings stay in place. This lets the bot remove roles that were previously managed. Do not delete a website role while it has a mapping.
Deleting a Discord role mapping stops managing that role. The role may remain assigned to members who already have it; disable the mapping instead when you want the bot to remove it.

The bot must change only roles listed in `managed_role_ids`. Add roles in `desired_role_ids`. Remove managed roles that are not in `desired_role_ids`. Do not change any other role.

## 4. Handle sync callbacks

The website queues a callback when an account link, role membership, donor perk, or mapping changes. The callback body contains:

```json
{
  "discord_user_id": "123456789012345678",
  "sync_id": "a-stable-id-for-this-callback"
}
```

The request includes `Idempotency-Key`, `X-Discord-Timestamp`, and `X-Discord-Signature` headers.

The signature is `sha256=` followed by the lowercase hex HMAC-SHA256 of `<timestamp>.<raw request body>`. Use `DISCORD_CALLBACK_SECRET` as the key. The bot must reject old timestamps and invalid signatures. It must safely handle repeated idempotency keys.

Callback data does not include website role information. After the callback, the bot must fetch the latest role state from the website.

## 5. Fetch and apply the desired roles

Request a Passport token, then call this endpoint with the token:

```text
GET /v3/discord/users/{discord_user_id}/roles
```

The response includes the Discord user ID, guild ID, link status, managed role IDs, and desired role IDs. `managed_role_ids` includes roles from disabled mappings.

The website calculates desired roles when the bot makes the request. It uses the user's current website roles, default role, active donor perks, and enabled mappings. If the account is unlinked, `linked` is `false` and `desired_role_ids` is empty. The bot can then remove the user's managed roles.

The bot must check that the user is in the guild, read the user's current roles, and follow Discord rate limits. It must handle missing users or roles and permission errors. Then it applies the role changes. Repeating the same sync must not cause unwanted changes.

## 6. Run the queue and troubleshoot

Run the website's existing Laravel queue worker. Callback jobs retry a limited number of times, with increasing delays, for network errors, server errors, and rate limits. Other client errors are logged and not retried. Laravel records final job failures.

- For final failures, check `failed_jobs` and the application logs for `Discord role sync callback failed after retries`.
- If the logs show `Discord role sync callback was rejected`, check the callback HTTP status, bot endpoint, and HMAC validation.
- A `503` from the role API means `DISCORD_GUILD_ID` is missing or invalid.
- A `401` means the token is invalid or uses the wrong client. A `403` means the token is missing the required scope.
- If Discord rejects a role change, check the bot's **Manage Roles** permission and role order.
- Existing Discord webhook notifications use `DISCORD_WEBHOOK_URL`. They are separate from account linking, Passport credentials, the callback secret, and the bot token.
