import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import moment from 'moment';
import Loader from 'halogen/ScaleLoader';

import * as api from './api';
import * as constants from './constants';

export default class ServerFeed extends Component {
    constructor(props) {
        super(props);

        this.state = {
            viewState: constants.STATE_INIT,
            categories: [],
        };

        this.handleServerFetch = this.handleServerFetch.bind(this);
        this.renderServerCategory = this.renderServerCategory.bind(this);
    }

    componentDidMount() {
        this.setState({ viewState: constants.STATE_FETCHING });
        this.handleServerFetch();

        setInterval(this.handleServerFetch, constants.FETCH_INTERVAL_MILLISECONDS);
    }

    handleServerFetch() {
        api.getServerList()
            .then(response => {
                const { data } = response.data;

                this.setState({ 
                    viewState: constants.STATE_READY,
                    categories: data,
                });
            })
            .catch(error => {
                console.log(error);
                this.setState({ viewState: constants.STATE_FETCH_FAILED });
            });
    }
    
    renderServerCategory(category) {
        const { servers } = category;

        if(servers.length === 0) {
            return (<div key={category.server_category_id} />);
        }

        const serverList = servers.map(server => {
            const ip = (server.port && server.is_port_visible)
                ? `${server.ip}:${server.port}`
                : server.ip;

            let ipAlias = server.ip_alias != null ? `${server.ip_alias} / ` : '';

            const { status } = server;
            const isOnline = status != null && status.is_online;
            const playersOnline = isOnline
                ? `${status.num_of_players}/${status.num_of_slots}`
                : 'Offline';

            return (
                <server className={isOnline ? 'online' : 'offline'} key={server.server_id}>
                    <div className="status">{playersOnline}</div>
                    <div className="title">{server.name}</div>
                    <div className="ip">{ipAlias}{ip}</div>
                </server>
            );
        });

        return (
            <server-category key={category.server_category_id}>
                <h5>{category.name}</h5>
                {serverList}
            </server-category>
        );
    }

    render() {
        const { viewState, categories } = this.state;

        let contents;
        if(viewState === constants.STATE_READY) {
            contents = categories.map(category => this.renderServerCategory(category));
        
        } else if(viewState === constants.STATE_FETCHING) {
            contents = (
                <Loader color="#fff" size="18px" margin="4px"/>
            );
        }

        return (
            <div>
                {contents}
            </div>
        );
    }
}

if (document.getElementById('serverfeed')) {
    ReactDOM.render(<ServerFeed />, document.getElementById('serverfeed'));
}