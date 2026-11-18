declare namespace App {
    namespace Core {
        namespace Domains {
            namespace Auditing {
                namespace Changes {
                    namespace ArrayDiff {
                        export type ArrayWrapState =
                            "added" | "kept" | "removed";
                    }
                }
            }
            namespace Environment {
                export type EnvironmentLevel =
                    "production" | "staging" | "testing" | "local";
            }
        }
    }
    namespace Domains {
        namespace BanAppeals {
            namespace Data {
                export type BanAppealStatus = 0 | 1 | 2 | 3;
            }
        }
        namespace Bans {
            namespace Data {
                export type UnbanType = "expired" | "manual" | "appealed";
            }
        }
        namespace BuilderRankApplications {
            namespace Data {
                export type ApplicationStatus = 1 | 2 | 3;
                export type BuilderRank = 0 | 1 | 2 | 3;
            }
        }
        namespace Permissions {
            export type WebManagePermission =
                | "web.manage.roles.view"
                | "web.manage.roles.edit"
                | "web.manage.roles.assign"
                | "web.manage.permissions.view"
                | "web.manage.permissions.edit"
                | "web.manage.permissions.assign"
                | "web.manage.accounts.view"
                | "web.manage.accounts.edit"
                | "web.manage.accounts.view_email"
                | "web.manage.uuid_bans.view"
                | "web.manage.uuid_bans.edit"
                | "web.manage.ip_bans.view"
                | "web.manage.ip_bans.edit"
                | "web.manage.warnings.view"
                | "web.manage.warnings.edit"
                | "web.manage.players.view"
                | "web.manage.players.edit"
                | "web.manage.players.view_ips"
                | "web.manage.badges.view"
                | "web.manage.badges.edit"
                | "web.manage.warps.view"
                | "web.manage.warps.edit"
                | "web.manage.homes.view"
                | "web.manage.homes.edit"
                | "web.manage.servers.view"
                | "web.manage.servers.edit"
                | "web.manage.server_tokens.view"
                | "web.manage.server_tokens.edit"
                | "web.manage.remote_config.edit"
                | "web.manage.donations.view"
                | "web.manage.donations.edit"
                | "web.manage.player_mail.send";
            export type WebReviewPermission =
                | "web.review.ban_appeals.view"
                | "web.review.ban_appeals.decide"
                | "web.review.build_rank_apps.view"
                | "web.review.build_rank_apps.decide";
        }
        namespace PlayerMail {
            namespace Data {
                export type MailCategory = "direct" | "announcement" | "system";
                export type PlayerMailBroadcastStatus =
                    "queued" | "processing" | "completed" | "failed";
            }
        }
        namespace Players {
            namespace Data {
                export type MoveMode = "walk" | "fly";
                export type TeleportMode = "off" | "request" | "auto";
            }
        }
    }
    namespace Http {
        namespace Data {
            export type AdminAccountData = {
                id: number;
                username: string;
                email: string;
                activated: boolean;
                terms_accepted: number | boolean | null;
                last_login_at: string | null;
                is_totp_enabled: boolean;
                created_at: string | null;
                updated_at: string | null;
                roles?: Array<any> | null;
                badges?: Array<any> | null;
                minecraft_account?: Array<any> | null;
                donations?: Array<any> | null;
                email_change_requests?: Array<any> | null;
                activations?: Array<any> | null;
            };
            export type AdminAccountSummaryData = {
                name: string;
                avatarUuid: string | null;
            };
            export type AdminBanAppealData = {
                id: number;
                status: App.Domains.BanAppeals.Data.BanAppealStatus | null;
                email: string | null;
                minecraft_uuid: string | null;
                date_of_ban: string | null;
                ban_reason: string | null;
                unban_reason: string | null;
                decided_at: string | null;
                decision_note: string | null;
                account?: App.Http.Data.AdminAccountData | null;
                game_player_ban?: Array<any> | null;
                decider_player?: Array<any> | null;
                created_at: string | null;
            };
            export type AdminSharedPagePropsData = {
                account?: App.Http.Data.AdminAccountSummaryData | null;
                success?: string | null;
                pending_appeals?: number | null;
                pending_build_rank_apps?: number | null;
                is_admin: boolean;
                permissions: string[] | null;
            };
            export type ManageHomeRecentActivityData = {
                id: string;
                actor: string;
                avatarUuid: string | null;
                action: string;
                event: string;
                subject: string;
                createdAt: string;
                when: string;
            };
            export type MinecraftBuildData = {
                id: number;
                player_id: number;
                name: string;
                description: string | null;
                lore: string | null;
                votes: number;
                world: string;
                x: number;
                y: number;
                z: number;
                pitch: string;
                yaw: string;
                created_at: string | null;
                updated_at: string | null;
                player?: App.Http.Data.MinecraftPlayerData | null;
            };
            export type MinecraftConfigApiData = {
                id: number | null;
                config: object | null;
                version: number | null;
                created_at: string | null;
                updated_at: string | null;
                deleted_at: string | null;
            };
            export type MinecraftConfigPageData = {
                version: number;
                config: Record<string, any>;
                created_at: string;
                updated_at: string;
            };
            export type MinecraftHomeData = {
                id: number;
                player_id: number;
                name: string;
                world: string;
                x: number;
                y: number;
                z: number;
                pitch: string;
                yaw: string;
                created_at: string | null;
                updated_at: string | null;
                player?: App.Http.Data.MinecraftPlayerData | null;
            };
            export type MinecraftPlayerData = {
                id: number;
                uuid: string;
                alias: string | null;
                nickname: string | null;
                muted: number;
                walk_speed: number;
                fly_speed: number;
                teleport_mode: App.Domains.Players.Data.TeleportMode | null;
                move_mode: App.Domains.Players.Data.MoveMode | null;
                last_location_world_uuid: string | null;
                last_location_x: number | null;
                last_location_y: number | null;
                last_location_z: number | null;
                last_location_pitch: number | null;
                last_location_yaw: number | null;
                sessions: number;
                play_time: number;
                afk_time: number;
                blocks_placed: number;
                blocks_destroyed: number;
                blocks_travelled: number;
                account_id: number | null;
                last_seen_at: string | null;
                last_connected_at: string | null;
                created_at: string | null;
                updated_at: string | null;
            };
            export type MinecraftWarpData = {
                id: number;
                name: string;
                world: string;
                x: number;
                y: number;
                z: number;
                pitch: string;
                yaw: string;
                created_at: string | null;
                updated_at: string | null;
            };
            export type PlayerMailApiData = {
                id: number;
                category: App.Domains.PlayerMail.Data.MailCategory;
                sender: string;
                subject: string;
                body: string;
                read_at: string | null;
                received_at: string | null;
            };
            export type PlayerMailBroadcastPageData = {
                id: number;
                subject: string;
                body: string;
                sender: string;
                status: App.Domains.PlayerMail.Data.PlayerMailBroadcastStatus;
                recipients_count: number;
                created_at: string;
                started_at: string | null;
                completed_at: string | null;
            };
            export type PlayerMailBroadcastSummaryData = {
                id: number;
                subject: string;
                sender: string;
                recipients_count: number;
                status: App.Domains.PlayerMail.Data.PlayerMailBroadcastStatus;
                created_at: string;
            };
            export type PlayerMailComposePageData = {
                recipientCount: number;
                sender: string;
            };
            export type PlayerMailInboxData = {
                id: number;
                category: App.Domains.PlayerMail.Data.MailCategory;
                sender: string;
                subject: string;
                read_at: string | null;
                received_at: string | null;
            };
        }
    }
}
declare namespace Illuminate {
    export type CursorPaginator<TKey, TValue> = {
        data: TKey extends string ? Record<TKey, TValue> : TValue[];
        links: {
            url: string | null;
            label: string;
            active: boolean;
        }[];
        meta: {
            path: string;
            per_page: number;
            next_cursor: string | null;
            next_page_url: string | null;
            prev_cursor: string | null;
            prev_page_url: string | null;
        };
    };
    export type CursorPaginatorInterface<TKey, TValue> =
        Illuminate.CursorPaginator<TKey, TValue>;
    export type LengthAwarePaginator<TKey, TValue> = {
        data: TKey extends string ? Record<TKey, TValue> : TValue[];
        links: {
            url: string | null;
            label: string;
            active: boolean;
        }[];
        meta: {
            total: number;
            current_page: number;
            first_page_url: string;
            from: number | null;
            last_page: number;
            last_page_url: string;
            next_page_url: string | null;
            path: string;
            per_page: number;
            prev_page_url: string | null;
            to: number | null;
        };
    };
    export type LengthAwarePaginatorInterface<TKey, TValue> =
        Illuminate.LengthAwarePaginator<TKey, TValue>;
}
declare namespace Spatie {
    namespace LaravelData {
        export type CursorPaginatedDataCollection<TKey, TValue> =
            Illuminate.CursorPaginator<TKey, TValue>;
        export type PaginatedDataCollection<TKey, TValue> =
            Illuminate.LengthAwarePaginator<TKey, TValue>;
    }
}
