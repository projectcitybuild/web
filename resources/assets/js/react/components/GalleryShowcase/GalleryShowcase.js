import React, { Component } from 'react';
import ReactDOM from 'react-dom';

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
            this.setState({
                images: response.data.data,
            }, () => console.log(this.state));
        });
    }

    render() {
        return (
            <div className="panel gallery">
                <div className="picture-frame">
                    {this.state.images && this.state.images.length > 0 && 
                        <img src={this.state.images[this.state.index].link} />
                    }
                </div>
                <div className="picture-desc">
                    <h3>Zurich</h3>
                    {this.state.images && this.state.images.length > 0 &&
                        <small>
                            {this.state.images[this.state.index].description}
                        </small>
                    }
            
                    <h5>September - Staff Picks</h5>
                </div>
            </div>
        );
    }
}

if (document.getElementById('showcase')) {
    ReactDOM.render(<GalleryShowcase />, document.getElementById('showcase'));
}