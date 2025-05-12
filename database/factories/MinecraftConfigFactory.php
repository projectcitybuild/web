<?php

namespace Database\Factories;

use App\Models\MinecraftConfig;

class MinecraftConfigFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MinecraftConfig::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'version' => 1,
            'config' => [
                'localization' => [
                    'time_zone' => 'UTC',
                    'locale' => 'en-us',
                ],
                'chat' => [
                    'badge_icon' => '<gold>★</gold>',
                    'staff_channel' => '<yellow>(Staff) <name>:</yellow> <message>',
                ],
                'warps' => [
                    'items_per_page' => 15,
                ],
                'integrations' => [
                    'dynmap_warp_icon_name' => 'portal',
                ],
                'announcements' => [
                    'interval_in_mins' => 30,
                    'messages' => [
                        '<aqua>Join the Project City Build Discord server! Type /discord in game!</aqua>',
                        '<aqua>Donations are the only way to keep PCB running! If you would like to donate to the server, you can do so by typing /donate in game!</aqua>',
                        '<aqua>Vote for us to help keep PCB active! Type /vote in game!</aqua>',
                        '<aqua>Post screenshots of your builds to the #showcase channel on our Discord to be featured on the PCB Instagram! Type /discord to join!</aqua>',
                        '<aqua>Make sure to follow the Project City Build Instagram for features of YOUR builds! Type /instagram in game!</aqua>',
                    ],
                ],
                'messages' => [
                    'join' => '<green><bold>+</bold></green> <name> <gray>joined the server',
                    'leave' => '<red><bold>-</bold></red> <name> <gray>left the server (online for <time_online>)',
                    'first_time_join' => '<light_purple>✦ Welcome <white><name></white> <light_purple>to the server!',
                    'welcome' => '<gray>Welcome to </gray><white><bold>PROJECT </bold></white><gold><bold>CITY </bold></gold><blue><bold>BUILD</bold></blue><newline><gray>| </gray><newline><gray>| </gray><white>Type <red><bold>/menu </bold></red><white>to access most server features, including rank<newline><gray>| </gray><white>applications, warps, player reporting, and other information.<newline><gray>| </gray><gray>Hold down the <red><bold>TAB </bold><gray>key to see who else is online.<newline><gray>| </gray><white>Ask our staff if you have any questions.</white>',
                ],
                'motd' => '<bold><white>PROJECT </white><gold>CITY </gold><blue>BUILD</blue></bold><newline><gray>14+ years of building! </gray><bold><white>></white><gold>></gold><blue>></blue></bold> <white>Come join us!   </white><dark_gray>[ <gold><bold>1.21.4</bold></gold> <dark_gray>]',
                'tab' => [
                    'header' => [
                        '<gradient:dark_aqua:green>────────────>>> <bold><white>Project</white> <gold>City</gold> <blue>Build</blue></bold> <<<────────────</gradient>',
                        '<gray>World:</gray> <aqua><world></aqua>',
                        '<gray>Online:</gray> <aqua><player_count>/<max_players></aqua>',
                        '<group_list>',
                        '',
                    ],
                    'footer' => [
                        '',
                        '<gray>Join us on <bold>/discord</bold></gray>',
                        '<gray>projectcitybuild.com</gray>',
                        '<gradient:dark_aqua:green>────────────────────────────────────────</gradient>',
                    ],
                    'player' => '<groups><name><afk><spacer><ping>',
                    'player_column_length' => 32,
                ],
                'server_links' => [
                    'website' => 'https://projectcitybuild.com',
                    'custom' => [
                        'discord' => 'https://discord.gg/3NYaUeScDX',
                    ],
                ],
            ],
        ];
    }
}

