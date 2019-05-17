const config = require('../../config/manish.js');
import React, { Component } from 'react';

class CreateItem extends Component {
    constructor(props) {
        super(props);
        this.state = { productName: '', productPrice: '' };
    }
    handleNameChange(e) {
        this.setState({
            productName: e.target.value
        })
    }
    handlePriceChange(e) {
        this.setState({
            productPrice: e.target.value
        })
    }
    handleSubmit(e) {
        e.preventDefault();
        const products = {
            name: this.state.productName,
            price: this.state.productPrice
        }
        let uri = `${config.url.api}items`;
        axios.post(uri, products).then((response) => {
            this.props.history.push(config.url.path+'display-item');
        });
    }

    render() {
        return (
            <div>
                <h1>Create An Item</h1>
                <form onSubmit={(e)=>this.handleSubmit(e)}>
                    <div className="row">
                        <div className="col-md-6">
                            <div className="form-group">
                                <label>Item Name:</label>
                                <input type="text" className="form-control" onChange={(e)=>this.handleNameChange(e)} />
                            </div>
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-md-6">
                            <div className="form-group">
                                <label>Item Price:</label>
                                <input type="text" className="form-control col-md-6" onChange={(e)=>this.handlePriceChange(e)} />
                            </div>
                        </div>
                    </div><br />
                    <div className="form-group">
                        <button className="btn btn-primary">Add Item</button>
                    </div>
                </form>
            </div>
        )
    }
}
export default CreateItem;