const config = require('../../config/manish.js');
import React, { Component } from 'react';
import { Link } from 'react-router-dom';

class Master extends Component {
    render() {
        return (
            <div className="container">
                <nav className="navbar navbar-default">
                    <div className="container-fluid">
                        <div className="navbar-header">
                            <a className="navbar-brand" href={config.url.path}>Home</a>
                        </div>
                        <ul className="nav navbar-nav">
                            <li><Link to={config.url.path+"items"}>Home</Link></li>
                            <li><Link to={config.url.path+"add-item"}>Create Item</Link></li>
                            <li><Link to={config.url.path+"display-item"}>Display Item</Link></li>
                        </ul>
                    </div>
                </nav>
                <div>
                    {this.props.children}
                </div>
            </div>
        )
    }
}
export default Master;