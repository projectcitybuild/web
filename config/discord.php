<?php

return [
    /**
     * Channel for architects to review build rank applications
     */
    'webhook_architect_forum_channel' => env('DISCORD_WEBHOOK_ARCHITECT_FORUM'),

    /**
     * Channel for architect notifications
     */
    'webhook_architect_chat_channel' => env('DISCORD_WEBHOOK_ARCHITECT_CHAT'),

    /**
     * Channel for ban appeal submissions
     */
    'webhook_ban_appeal_channel' => env('DISCORD_WEBHOOK_BAN_APPEAL'),

    /**
     * Channel for contact form submissions
     */
    'webhook_contact_channel' => env('DISCORD_WEBHOOK_CONTACT'),

    /**
     * Channel for send OP elevation audit logs
     */
    'webhook_op_elevation_channel' => env('DISCORD_WEBHOOK_OP_ELEVATION'),

    /**
     * Invite URL to join our Discord server
     */
    'invite_url' => 'https://discord.gg/3NYaUeScDX',
];
