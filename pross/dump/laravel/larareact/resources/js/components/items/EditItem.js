const config = require('../config/manish.js');
import React, { Component } from 'react';
import { Link } from 'react-router-dom';
import axios from 'axios';

class EditItem extends Component {
    constructor(props) {
        super(props);
        this.state = { name: '', price: '' };
    }

    componentDidMount(e) {
        let uri = `${config.url.api}items/${this.props.match.params.id}`;
        axios.get(uri)
        .then(response => {
            this.setState({ 
                name: response.data.name,
                price: response.data.price
            });
        })
        .catch(function (error) {
            console.log(error);
        })
    }
    handleNameChange(e) {
        this.setState({
            name: e.target.value
        })
    }
    handlePriceChange(e) {
        this.setState({
            price: e.target.value
        })
    }

    handleSubmit(event) {
        event.preventDefault();
        const products = {
            name: this.state.name,
            price: this.state.price
        }
        let uri = `${config.url.api}items/${this.props.match.params.id}`;
        axios.patch(uri, products).then((response) => {
            this.props.history.push(config.url.path+'items');
        });
    }
    render() {
        return (
            <div>
                <h1>Update Item</h1>
                <div className="row">
                    <div className="col-md-10"></div>
                    <div className="col-md-2">
                        <Link to={config.url.path+"items"} className="btn btn-success">Return to Items</Link>
                    </div>
                </div>
                <form onSubmit={(e)=>this.handleSubmit(e)}>
                    <div className="form-group">
                        <label>Item Name</label>
                        <input type="text"
                            className="form-control"
                            value={this.state.name}
                            onChange={(e)=>this.handleNameChange(e)} />
                    </div>

                    <div className="form-group">
                        <label name="product_price">Item Price</label>
                        <input type="text" className="form-control"
                            value={this.state.price}
                            onChange={(e)=>this.handlePriceChange(e)} />
                    </div>

                    <div className="form-group">
                        <button className="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        )
    }
}
export default EditItem;