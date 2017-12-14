import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import ReactCSSTransitionGroup from 'react-addons-css-transition-group';

import * as api from './api';

export default class GalleryShowcase extends Component {
    constructor(props) {
        super(props);

        this.state = {
            images: [],
            index: 0,
        };
    }

    componentDidMount() {
        api.getAlbum(response => {
            const { data = {} } = response.data;
            if(data.length === 0) {
                return;
            }

            this.setState({
                images: data,
            }, () => console.log(this.state));
        });

        setInterval(() => {
            const { index, images } = this.state;
            this.setState({
                index: (index + 1) % images.length,
            });
        }, 3000);
    }

    render() {
        const { images = [], index } = this.state;

        const visibleImage = images[index] || {};
        const {
            link = '',
            id = '',
            description = '',
            title = '',
        } = visibleImage;
        
        return (
            <div className="panel gallery">
                <div className="picture-frame">
                    <div className="showcase-container">
                        <ReactCSSTransitionGroup
                            transitionName="showcase"
                            transitionEnterTimeout={500}
                            transitionLeaveTimeout={300}>
                            
                            <img className="showcase" src={link} key={id} />
                        </ReactCSSTransitionGroup>
                    </div>
                </div>
                <div className="picture-desc">
                    <h3>{title}</h3>
                    <small>
                        {description}
                    </small>
            
                    <h5>Staff Picks</h5>
                </div>
            </div>
        );
    }
}

if (document.getElementById('showcase')) {
    ReactDOM.render(<GalleryShowcase />, document.getElementById('showcase'));
}