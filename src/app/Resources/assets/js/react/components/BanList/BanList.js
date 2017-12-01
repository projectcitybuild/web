import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import moment from 'moment';
import Loader from 'halogen/ScaleLoader';

import * as api from './api';
import * as constants from './constants';

export default class BanList extends Component {
    constructor(props) {
        super(props);

        this.state = {
            bans: [],
            servers: [],
            aliases: [],
            viewState: constants.STATE_INIT,
            totalBans: 0,
            page: 1,
            sort: { 
                field: 'created_at',
                direction: 'DESC',
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
     * 
     * @param object filters 
     */
    handleFetch(sortBy = this.state.sort, filters = {}) {
        this.setState({ viewState: constants.STATE_FETCHING });

        api.getBanList(1, sortBy, filters)
            .then(response => {
                const { data } = response;
                this.setState({
                    bans: data.data,
                    servers: data.relations.servers,
                    aliases: data.relations.aliases,
                    viewState: constants.STATE_READY,
                    totalBans: data.meta.count,
                    page: 1,
                });
            })
            .catch(error => {
                console.log(error);
                this.setState({ viewState: constants.STATE_FETCH_FAILED });
            });
    }

    /**
     * Fetches the next page of the ban list, reusing any currently set filters
     */
    handlePaginateFetch() {
        api.getBanList(this.state.page + 1, this.state.sort)
            .then(response => {
                const { data } = response;
                this.setState({
                    bans: this.state.bans.concat(data.data),
                    servers: Object.assign({}, this.state.servers, data.relations.servers),
                    aliases: Object.assign({}, this.state.aliases, data.relations.aliases),
                    viewState: constants.STATE_READY,
                    page: this.state.page + 1,
                });
            })
            .catch(error => {
                console.log(error);
                this.setState({ viewState: constants.STATE_FETCH_FAILED });
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
            if(!this.isEndOfData() && this.state.viewState == constants.STATE_READY) {
                // call fetch after state has updated to prevent race
                this.setState({ viewState: constants.STATE_FETCHING_PAGE }, () => this.handlePaginateFetch());
            }
        }
    }

    /**
     * Returns whether fetching the next page from the api would yield any data
     * @return bool
     */
    isEndOfData() {
        return this.state.bans.length >= this.state.totalBans;
    }

    /**
     * Sorts the ban list by the given field
     * 
     * @param object event 
     * @param string field 
     */
    sortBy(event, field) {
        event.preventDefault();

        const { sort } = this.state;

        if(sort.field === field) {
            this.setState({
                sort: {
                    field: sort.field,
                    direction: sort.direction === 'DESC' ? 'ASC' : 'DESC',
                },
            }, () => this.handleFetch());

        } else {
            this.setState({
                sort: {
                    field: field,
                    direction: 'DESC',
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
    renderRow(ban, index) {
        const { servers, aliases, totalBans } = this.state;

        const createdAt = moment.unix(ban.created_at).format('llll');
        const expiresAt = ban.expires_at ? moment.unix(ban.expires_at).format('llll') : '-';

        const playerAlias = aliases[ban.banned_alias_id];
        const server = servers[ban.server_id];

        let avatar;
        if(server.game_type == 'minecraft') {
            avatar = <img src={'https://minotar.net/helm/'+ ban.player_alias_at_ban +'/16'} width="16" height="16" />
        }

        return (
            <tr key={ban.game_ban_id} className={ !ban.is_active ? 'inactive' : '' }>
                <td>{totalBans - index}</td>
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
        const { bans, totalBans, sort } = this.state;
        const banList = bans.map((ban, index) => this.renderRow(ban, index));

        const caret = <i className={sort.direction === 'DESC' ? 'fa fa-caret-down' : 'fa fa-caret-up'}></i>;

        return (
            <div className="panel banlist-layout" id="banlistComponent">
                <div className="ban-header">
                    The below <strong>{totalBans}</strong> players are currently banned from connecting to our game network.
                </div>
            
                <div className="ban-contents">
                    <div className="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <td>#</td>
                                    <td>Banned Identifier</td>
                                    <td colSpan="2">
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
                    {this.state.viewState === constants.STATE_FETCHING && this.state.bans.length === 0 &&
                        <div className="loadContainer">
                            <Loader color="#F5A503" size="18px" margin="4px"/>
                        </div>
                    }
                    {this.state.viewState === constants.STATE_FETCHING_PAGE &&
                        <div className="loadContainer">
                            <Loader color="#F5A503" size="18px" margin="4px"/>
                        </div>         
                    }
                </div>
            </div>
        );
    }
}

if (document.getElementById('banlist')) {
    ReactDOM.render(<BanList />, document.getElementById('banlist'));
}