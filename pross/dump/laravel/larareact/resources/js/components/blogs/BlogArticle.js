const config = require('../../config/manish.js');
import React, { Component } from 'react';
import { BrowserRouter as Router, Link, Route } from 'react-router-dom';
import axios from 'axios';

export default class BlogArticle extends Component {
    constructor(props) {
        super(props);
        this.state = {
            post : []
        }
    }

    componentDidMount() {
        axios.get(config.url.api+'blog/'+this.props.match.params.id)
        .then(response => {
            this.setState({
                post: response.data[0]
            })
        })
        .catch(errors => {
            console.log(errors)
        });
    }

    render() {
        //console.log(this.props)
        return (
            <div className="container">
                <h1>{this.state.post.title}</h1>
                <h3>{this.state.post.details}</h3>
            </div>
        );
    }
}
