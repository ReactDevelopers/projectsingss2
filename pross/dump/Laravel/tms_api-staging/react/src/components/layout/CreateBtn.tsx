import * as React from 'react';
import { Button } from 'react-bootstrap';
import { NavLink } from 'react-router-dom';
var FAS = require('react-fontawesome');

interface CreateBtnProps {

    title?: string;
    url: string;
}

export default class CreateBtn extends React.Component<CreateBtnProps> {

    shouldComponentUpdate() {return false}
    
    render () {
        const { title, url} = this.props;

        return (
            <Button bsStyle="info" className="drop-link pull-right create-btn">
            <NavLink to={url} title={title}>
                <FAS name="plus" />
            </NavLink>
            </Button> 
        );
    }
}