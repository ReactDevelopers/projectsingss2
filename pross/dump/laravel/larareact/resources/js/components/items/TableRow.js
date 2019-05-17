const config = require('../../config/manish.js');
import React, { Component } from 'react';
import { BrowserRouter as Router, Link, Route } from 'react-router-dom';

class TableRow extends Component {
    constructor(props) {
        super(props);
    }
    handleSubmit(event) {
        event.preventDefault();
        let uri = `${config.url.api}items/${this.props.obj.id}`;
        axios.delete(uri);
        this.props.deleteItem();
    }
    render() {
        return (
            <tr>
                <td>
                    {this.props.obj.id}
                </td>
                <td>
                    {this.props.obj.name}
                </td>
                <td>
                    {this.props.obj.price}
                </td>
                <td>
                    <Link to={"edit/" + this.props.obj.id} className="btn btn-primary">Edit</Link>
                </td>
                <td>
                    <form onSubmit={(e)=>this.handleSubmit(e)}>
                        <input type="submit" value="Delete" className="btn btn-danger" />
                    </form>
                </td>
            </tr>
        );
    }
}

export default TableRow;