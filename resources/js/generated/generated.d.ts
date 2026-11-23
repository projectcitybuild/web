declare namespace App {
    export namespace Core {
        export namespace Domains {
            export namespace Auditing {
                export namespace Changes {
                    export namespace ArrayDiff {
                        export type ArrayWrapState =
                            "added" | "kept" | "removed";
                    }
                }
            }
            export namespace Environment {
                export type EnvironmentLevel =
                    "production" | "staging" | "testing" | "local";
            }
        }
    }
    export namespace Domains {
        export namespace BanAppeals {
            export namespace Data {
                export type BanAppealStatus =
                    | "pending"
                    | "accepted_unban"
                    | "accepted_tempban"
                    | "denied";
            }
        }
        export namespace Bans {
            export namespace Data {
                export type UnbanType = "expired" | "manual" | "appealed";
            }
        }
        export namespace BuilderRankApplications {
            export namespace Data {
                export type ApplicationStatus =
                    "pending" | "approved" | "denied";
                export type BuilderRank = 0 | 1 | 2 | 3;
            }
        }
        export namespace Permissions {
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
        export namespace PlayerMail {
            export namespace Data {
                export type MailCategory = "direct" | "announcement" | "system";
                export type PlayerMailBroadcastStatus =
                    "queued" | "processing" | "completed" | "failed";
            }
        }
        export namespace Players {
            export namespace Data {
                export type MoveMode = "walk" | "fly";
                export type TeleportMode = "off" | "request" | "auto";
            }
        }
    }
    export namespace Http {
        export namespace Data {
            export type AdminAccountActivationData = {
                id: number;
                account_id: number;
                created_at: string | null;
                updated_at: string | null;
                expires_at: string;
            };
            export type AdminAccountBadgeData = {
                id: number;
                display_name: string;
                unicode_icon: string;
                list_hidden: boolean;
                created_at: string | null;
                updated_at: string | null;
                accounts_count?: number | null;
                pivot?: App.Http.Data.AdminAccountBadgePivotData | null;
            };
            export type AdminAccountBadgePivotData = {
                account_id: number;
                badge_id: number;
            };
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
                roles: App.Http.Data.AdminAccountRoleData[] | null;
                badges: App.Http.Data.AdminAccountBadgeData[] | null;
                minecraft_account: App.Http.Data.MinecraftPlayerData[] | null;
                discord_account: App.Http.Data.AdminDiscordAccountData | null;
                donations: App.Http.Data.AdminDonationData[] | null;
                email_change_requests:
                    App.Http.Data.AdminEmailChangeData[] | null;
                activations: App.Http.Data.AdminAccountActivationData[] | null;
            };
            export type AdminAccountRoleData = {
                id: number;
                name: string;
                alias: string | null;
                is_default: boolean;
                is_admin: boolean;
                minecraft_name: string | null;
                minecraft_display_name: string;
                minecraft_hover_text: string;
                display_priority: number | null;
                role_type: string | null;
                additional_homes: number | null;
                pivot?: App.Http.Data.AdminAccountRolePivotData | null;
            };
            export type AdminAccountRolePivotData = {
                account_id: number;
                role_id: number;
            };
            export type AdminAccountSummaryData = {
                name: string;
                avatarUuid: string | null;
            };
            export type AdminAuditArrayEntryData = {
                state: string;
                text: string;
            };
            export type AdminAuditChangeData = {
                attribute: string;
                description: string;
                oldValue: App.Http.Data.AdminAuditValueData;
                newValue: App.Http.Data.AdminAuditValueData;
                oldEntries: App.Http.Data.AdminAuditArrayEntryData[] | null;
                newEntries: App.Http.Data.AdminAuditArrayEntryData[] | null;
            };
            export type AdminAuditDetailData = {
                id: number;
                actor: string;
                subject: App.Http.Data.AdminAuditValueData;
                event: string;
                createdAt: string;
                changes: App.Http.Data.AdminAuditChangeData[];
            };
            export type AdminAuditListEntryData = {
                id: number;
                actor: string;
                action: string;
                event: string;
                subject: string;
                createdAt: string;
                showUrl: string;
            };
            export type AdminAuditTypeData = {
                value: string;
                label: string;
            };
            export type AdminAuditValueData = {
                text: string;
                href: string | null;
                kind: string;
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
                game_player_ban?: App.Http.Data.AdminGamePlayerBanData | null;
                decider_player?: App.Http.Data.MinecraftPlayerData | null;
                created_at: string | null;
            };
            export type AdminBuilderRankApplicationData = {
                id: number;
                account_id: number;
                minecraft_alias: string;
                current_builder_rank: string;
                build_location: string;
                build_description: string;
                additional_notes: string | null;
                status: App.Domains.BuilderRankApplications.Data.ApplicationStatus;
                denied_reason: string | null;
                closed_at: string | null;
                created_at: string | null;
                updated_at: string | null;
                account?: App.Http.Data.AdminAccountData | null;
            };
            export type AdminDiscordAccountData = {
                provider_user_id: string;
                username: string | null;
            };
            export type AdminDonationData = {
                id: number;
                account_id: number | null;
                amount: number;
                created_at: string | null;
                updated_at: string | null;
                account?: App.Http.Data.AdminAccountData | null;
                payment?: App.Http.Data.AdminPaymentData | null;
            };
            export type AdminDonationPerkData = {
                id: number;
                account_id: number;
                donation_id: number;
                donation_tier_id: number | null;
                is_active: boolean;
                expires_at: string | null;
                account_username: string | null;
                donation_tier_name: string | null;
            };
            export type AdminEmailChangeData = {
                id: number;
                account_id: number;
                token: string;
                email: string;
                created_at: string | null;
                updated_at: string | null;
                expires_at: string;
            };
            export type AdminGameIPBanData = {
                id: number;
                banner_player_id: number | null;
                unbanner_player_id: number | null;
                ip_address: string;
                reason: string | null;
                created_at: string | null;
                updated_at: string | null;
                unbanned_at: string | null;
                unban_type: App.Domains.Bans.Data.UnbanType | null;
                banner_player?: App.Http.Data.MinecraftPlayerData | null;
                unbanner_player?: App.Http.Data.MinecraftPlayerData | null;
            };
            export type AdminGamePlayerBanData = {
                id: number;
                banned_player_id: number;
                banned_alias_at_time: string | null;
                banner_player_id: number | null;
                reason: string | null;
                additional_info: string | null;
                expires_at: string | null;
                created_at: string | null;
                updated_at: string | null;
                unbanned_at: string | null;
                unbanner_player_id: number | null;
                unban_type: App.Domains.Bans.Data.UnbanType | null;
                banned_player: App.Http.Data.MinecraftPlayerData;
                banner_player?: App.Http.Data.MinecraftPlayerData | null;
                unbanner_player?: App.Http.Data.MinecraftPlayerData | null;
            };
            export type AdminMinecraftPlayerReferenceData = {
                id: number;
                alias: string | null;
            };
            export type AdminPaymentData = {
                payment_id: number;
                paid_currency: string;
                paid_unit_amount: number;
                original_currency: string;
                original_unit_amount: number;
                unit_quantity: number;
                created_at: string | null;
                updated_at: string | null;
            };
            export type AdminPermissionData = {
                id: number;
                name: string;
            };
            export type AdminPlayerOpElevationData = {
                id: number;
                player: App.Http.Data.AdminMinecraftPlayerReferenceData;
                reason: string;
                startedAt: string;
                endedAt: string;
                isActive: boolean;
            };
            export type AdminPlayerWarningData = {
                id: number;
                warned_player_id: number;
                warner_player_id: number;
                reason: string;
                additional_info: string | null;
                weight: number;
                is_acknowledged: boolean;
                acknowledged_at: string | null;
                created_at: string | null;
                updated_at: string | null;
                warned_player: App.Http.Data.MinecraftPlayerData;
                warner_player: App.Http.Data.MinecraftPlayerData;
            };
            export type AdminRoleData = {
                id: number;
                name: string;
                alias: string | null;
                is_default: boolean;
                is_admin: boolean;
                minecraft_name: string | null;
                minecraft_display_name: string | null;
                minecraft_hover_text: string | null;
                display_priority: number | null;
                role_type: string | null;
                additional_homes: number | null;
                accounts_count?: number | null;
                permissions_count?: number | null;
            };
            export type AdminServerData = {
                id: number;
                name: string;
                ip: string;
                ip_alias: string | null;
                port: string | null;
                web_port: string | null;
                created_at: string | null;
                updated_at: string | null;
            };
            export type AdminServerTokenData = {
                id: number;
                token: string;
                description: string | null;
                allowed_ips: string | null;
                server_id: number;
                created_at: string | null;
                updated_at: string | null;
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
                config: JsonValue;
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
                account?: App.Http.Data.AdminAccountData | null;
            };
            export type MinecraftPlayerIpData = {
                id: number;
                player_id: number;
                ip: string;
                times_connected: number;
                created_at: string | null;
                updated_at: string | null;
            };
            export type MinecraftPlayerSessionData = {
                id: number;
                player_id: number;
                seconds: number;
                starts_at: string;
                ends_at: string;
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
    export namespace LaravelData {
        export type CursorPaginatedDataCollection<TKey, TValue> =
            Illuminate.CursorPaginator<TKey, TValue>;
        export type PaginatedDataCollection<TKey, TValue> =
            Illuminate.LengthAwarePaginator<TKey, TValue>;
    }
}
