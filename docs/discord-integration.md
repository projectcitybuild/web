# Discord role sync

The website links accounts, decides which roles users should have, and maps website roles to Discord roles. A seperate bot makes Discord API calls. 

## 1. Create a Passport client

Add an OAuth client via the [manage](http://localhost/manage) panel, 

Or alternatively, run this command:

```shell
php artisan passport:client --client --name="Discord"
```

The command shows the client ID and secret only one-time. The value is hashed then stored in the Passport client table.

## 2. Create a webhook secret

Generate a random password. This will be needed by both the bot and the web server, and is used to verify that the incoming HTTP request to the bot's webhook is from the web server.

## 3. Configure Discord login and callback

Create a Discord application if one doesn't already exist.

Register this callback URI in the Discord application:

```text
<base url>/account/settings/discord/callback
```

Replace base url with `http://localhost` for local development. Production should
be set to `https://projectcitybuild.com`.

## 4. Configure .env variables

| Variable | Source/purpose |
| --- | --- |
| `DISCORD_CLIENT_ID` | OAuth client id on Discord |
| `DISCORD_CLIENT_SECRET` | OAuth client secret on Discord |
| `DISCORD_REDIRECT_URI` | Callback URL from step (3) |
| `DISCORD_GUILD_ID` | Discord server id. Right click the server and choose 'Copy Server ID' |
| `DISCORD_PASSPORT_CLIENT_ID` | Passport client secret generated in step (1) |
| `DISCORD_BOT_CALLBACK_URL` | Bot webhook endpoint to notify that a user's role needs to be synced. This will typically be `http://pcb-discord-bot:3000/webhooks/discord/role-sync` when running both as containers on the same network. |
| `DISCORD_CALLBACK_SECRET` | Secret generated in step (2). Used by the bot to verify that a HTTP request came from our web server. |

Also ensure that `SESSION_CONNECTION=default` is set.

## 5. Map roles and set bot permissions

1. Open **Manage → Discord Role Mappings**.
2. Add a mapping for each website role and Discord role.
3. Give the bot **Manage Roles** permission. 
4. Put the bot's highest role above every mapped role. A Discord bot cannot assign someone to a role equal or higher than its own role.
