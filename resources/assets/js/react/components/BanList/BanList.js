import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import moment from 'moment';

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
        };

        this.renderRow = this.renderRow.bind(this);
    }

    componentDidMount() {
        this.setState({ viewState: constants.STATE_FETCHING });

        api.getBanList()
            .then(response => {
                const { data } = response;
                this.setState({
                    bans: data.data,
                    servers: data.relations.servers,
                    aliases: data.relations.aliases,
                    viewState: constants.STATE_READY,
                    totalBans: data.meta.count,
                }, () => console.log(this.state));
            })
            .catch(error => {
                console.log(error);
                this.setState({ viewState: constants.STATE_FETCH_FAILED });
            });
    }

    renderRow(ban, index) {
        const { servers, aliases, totalBans } = this.state;

        const createdAt = moment.unix(ban.created_at).format('llll');
        const expiresAt = ban.expires_at ? moment.unix(ban.expires_at).format('llll') : '-';

        return (
            <tr key={ban.game_ban_id}>
                <td>{totalBans - index}</td>
                <td>{aliases[ban.banned_alias_id].alias}</td>
                <td>{ban.player_alias_at_ban}</td>
                <td>{ban.reason || '-'}</td>
                <td></td>
                <td>{createdAt}</td>
                <td>{expiresAt}</td>
                <td>{ban.is_global_ban && <i className="fa fa-check"></i>}</td>
                <td>{ban.is_active && <i className="fa fa-check"></i>}</td>
                <td>{servers[ban.server_id].name}</td>
            </tr>
        );
    }

    render() {
        const { bans, totalBans } = this.state;
        const banList = bans.map((ban, index) => this.renderRow(ban, index));

        return (
            <div className="panel banlist-layout">
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
                                    <td>Alias Used</td>
                                    <td>Reason</td>
                                    <td>Banned By</td>
                                    <td>Ban Date</td>
                                    <td>Expires</td>
                                    <td>Global Ban</td>
                                    <td>Ban Active</td>
                                    <td>Server Banned On</td>
                                </tr>
                            </thead>
                            <tbody>
                                {banList}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        );
    }
}

if (document.getElementById('banlist')) {
    ReactDOM.render(<BanList />, document.getElementById('banlist'));
}