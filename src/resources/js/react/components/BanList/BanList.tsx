import * as React from 'react';
import * as ReactDOM from 'react-dom';
import dateFns from 'date-fns';
// import Loader from 'halogen/ScaleLoader';

import { Ban, Server, Alias } from './models';
import { ApiResponse, getBanList } from './api';


export interface BanListProps {
    
}

interface BanListState {
    bans: Array<Ban>;
    servers: Array<Server>;
    aliases: Array<Alias>;
    viewState: ComponentState;
    sort: Sort;
    pagination: Pagination;
}

interface Pagination {
    page: number,
    totalItems: number;
}

export interface Sort {
    field: string;
    direction: SortDirection;
}

export enum SortDirection {
    DESC = 'desc',
    ASC = 'asc',
}

enum ComponentState {
    INIT,
    READY,
    FETCHING,
    FETCHING_PAGE,
    FETCH_FAILED,
}

interface ClickEvent extends React.MouseEvent<HTMLAnchorElement> {}


export class BanList extends React.Component<BanListProps, BanListState> {
    constructor(props: BanListProps) {
        super(props);

        this.state = {
            bans: [],
            servers: [],
            aliases: [],
            viewState: ComponentState.INIT,
            sort: { 
                field: 'created_at',
                direction: SortDirection.DESC,
            },
            pagination: {
                page: 1,
                totalItems: 0,
            },
        };

        this.renderRow = this.renderRow.bind(this);
        this.handleScroll = this.handleScroll.bind(this);
        this.handleFetch = this.handleFetch.bind(this);
        this.handlePaginateFetch = this.handlePaginateFetch.bind(this);
    }

    componentDidMount() {
        window.addEventListener('scroll', this.handleScroll);
        this.handleFetch();
    }

    componentWillUnmount() {
        window.removeEventListener('scroll', this.handleScroll);
    }

    /**
     * Fetches fresh data from the ban list (and resets back to the first page)
     */
    handleFetch(sort: Sort = this.state.sort) {
        this.setState({ viewState: ComponentState.FETCHING });

        getBanList(1, sort)
            .then((response: ApiResponse) => {
                const { data } = response;
                this.setState({
                    bans: data.data,
                    servers: data.relations.servers,
                    aliases: data.relations.aliases,
                    viewState: ComponentState.READY,
                    pagination: {
                        totalItems: data.meta.count,
                        page: 1,
                    }
                });
            })
            .catch(error => {
                console.log(error);
                this.setState({ viewState: ComponentState.FETCH_FAILED });
            });
    }

    /**
     * Fetches the next page of the ban list, reusing any currently set filters
     */
    handlePaginateFetch() {
        getBanList(this.state.pagination.page + 1, this.state.sort)
            .then((response: ApiResponse) => {
                const { data } = response;
                this.setState({
                    bans: this.state.bans.concat(data.data),
                    servers: Object.assign({}, this.state.servers, data.relations.servers),
                    aliases: Object.assign({}, this.state.aliases, data.relations.aliases),
                    viewState: ComponentState.READY,
                    pagination: {
                        ...this.state.pagination,
                        page: this.state.pagination.page + 1,
                    }
                });
            })
            .catch(error => {
                console.log(error);
                this.setState({ viewState: ComponentState.FETCH_FAILED });
            });
    }

    /**
     * Checks if the user has scrolled to the bottom of the component. If they
     * have, triggers a new api fetch of the next page
     */
    handleScroll() {
        const component = document.getElementById('banlistComponent');

        const scrollY = window.scrollY;
        const bottomOfComponent = component.offsetHeight - component.offsetTop;
        
        if(scrollY >= bottomOfComponent) {
            if(!this.isEndOfData && this.state.viewState == ComponentState.READY) {
                // call fetch after state has updated to prevent race
                this.setState({ viewState: ComponentState.FETCHING_PAGE }, () => this.handlePaginateFetch());
            }
        }
    }

    /**
     * Returns whether fetching the next page from the api would yield any data
     * @return bool
     */
    get isEndOfData() : boolean {
        return this.state.bans.length >= this.state.pagination.totalItems;
    }

    /**
     * Sorts the ban list by the given field
     * 
     * @param object event 
     * @param string field 
     */
    sortBy(event: ClickEvent, field: string) {
        event.preventDefault();

        const { sort } = this.state;

        if(sort.field === field) {
            this.setState({
                sort: {
                    field: sort.field,
                    direction: sort.direction === SortDirection.DESC ? SortDirection.ASC : SortDirection.DESC,
                },
            }, () => this.handleFetch());

        } else {
            this.setState({
                sort: {
                    field: field,
                    direction: SortDirection.DESC,
                },
            }, () => this.handleFetch());
        }
    }

    /**
     * Returns a single JSX <tr> row for a ban
     * 
     * @param {*} ban 
     * @param {*} index 
     */
    renderRow(ban: Ban, index: number) {
        const { servers, aliases, pagination } = this.state;
        const { totalItems } = pagination;

        const createdAt = dateFns.format(new Date(ban.created_at), 'llll');
        const expiresAt = ban.expires_at ? dateFns.format(new Date(ban.expires_at), 'llll') : '-';

        const playerAlias = aliases[ban.banned_alias_id];
        const server = servers[ban.server_id];

        let avatar;
        if(server.game_type == 'minecraft') {
            avatar = <img src={'https://minotar.net/helm/'+ ban.player_alias_at_ban +'/16'} width="16" height="16" />;
        }

        return (
            <tr key={ban.game_ban_id} className={ !ban.is_active ? 'inactive' : '' }>
                <td>{totalItems - index}</td>
                <td>{playerAlias.alias}</td>
                <td>{avatar}</td>
                <td>{ban.player_alias_at_ban}</td>
                <td>{ban.reason || '-'}</td>
                <td></td>
                <td>{createdAt}</td>
                <td>{expiresAt}</td>
                <td>{ban.is_global_ban && <i className="fa fa-check"></i>}</td>
                <td>{ban.is_active && <i className="fa fa-check"></i>}</td>
                <td>{server.name}</td>
            </tr>
        );
    }

    render() {
        const { bans, pagination, sort } = this.state;
        const { totalItems } = pagination;
        
        const banList = bans.map((ban, index) => this.renderRow(ban, index));

        const caret = <i className={sort.direction === SortDirection.DESC ? 'fa fa-caret-down' : 'fa fa-caret-up'}></i>;

        return (
            <div className="panel banlist-layout" id="banlistComponent">
                <div className="ban-header">
                    The below <strong>{totalItems}</strong> players are currently banned from connecting to our game network.
                </div>
            
                <div className="ban-contents">
                    <div className="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <td>#</td>
                                    <td>Banned Identifier</td>
                                    <td colSpan={2}>
                                        <a href="" onClick={e => this.sortBy(e, 'player_alias_at_ban')}>
                                            Alias Used {sort.field === 'player_alias_at_ban' && caret}
                                        </a>
                                    </td>
                                    <td>Reason</td>
                                    <td>
                                        <a href="" onClick={e => this.sortBy(e, 'staff_game_user_id')}>
                                            Banned By {sort.field === 'staff_game_user_id' && caret}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="" onClick={e => this.sortBy(e, 'created_at')}>
                                            Ban Date {sort.field === 'created_at' && caret}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="" onClick={e => this.sortBy(e, 'expires_at')}>
                                            Expires {sort.field === 'expires_at' && caret}
                                        </a>
                                    </td>
                                    <td>Global Ban</td>
                                    <td>Ban Active</td>
                                    <td>
                                        <a href="" onClick={e => this.sortBy(e, 'server_id')}>
                                            Server Banned On {sort.field === 'server_id' && caret}
                                        </a>
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                                {banList}
                            </tbody>
                        </table>
                    </div>
                    {this.state.viewState === ComponentState.FETCHING && this.state.bans.length === 0 &&
                        <div className="loadContainer">
                            {/* <Loader color="#F5A503" size="18px" margin="4px"/> */}
                        </div>
                    }
                    {this.state.viewState === ComponentState.FETCHING_PAGE &&
                        <div className="loadContainer">
                            {/* <Loader color="#F5A503" size="18px" margin="4px"/> */}
                        </div>         
                    }
                </div>
            </div>
        );
    }
}