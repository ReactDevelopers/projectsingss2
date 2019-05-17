const config = require('../../config/manish.js');
import React, { Component } from 'react';
import { BrowserRouter as Router, Link, Route } from 'react-router-dom';
import axios from 'axios';

export default class Blog extends Component {
    constructor() {
        super();
        this.state = {
            blogs : []
        }
    }

    componentWillMount() {
        axios.get(config.url.api+'blog')
        .then(response => {
            this.setState({
                blogs: response.data
            })
        })
        .catch(errors => {
            console.log(errors)
        });
    }

    render() {
        return (
            <div className="container">
                {this.state.blogs.map((b,i) => 
                    <li key={i}>
                        <Link to={config.url.path+'blog/'+b.id}>{b.title}</Link>
                    </li>
                )}
            </div>
        );
    }
}
