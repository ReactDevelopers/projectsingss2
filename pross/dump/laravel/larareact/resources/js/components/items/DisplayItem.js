const config = require('../../config/manish.js');
import React, { Component } from 'react';
import { Link } from 'react-router-dom';
import axios from 'axios';
import TableRow from './TableRow';

class DisplayItem extends Component {
	constructor(props) {
		super(props);
		this.state = { value: '', items: '' };
	}
	componentDidMount() {
		axios.get(`${config.url.api}items`)
			.then(response => {
				this.setState({ items: response.data });
			})
			.catch(function (error) {
				console.log(error);
			})
	}

	removeTableRow(i) {
		const items = this.state.items;
		items.splice(i,1);
		this.setState({
			items : items
		});
	}

	tabRow() {
		const deleteRow = this.removeTableRow.bind(this);
		if (this.state.items instanceof Array) {
			return this.state.items.map(function (object, i) {
				return <TableRow obj={object} key={i} deleteItem={(e)=>deleteRow(i)} />;
			})
		}
	}

	render() {
		return (
			<div>
				<h1>Items</h1>

				<div className="row">
					<div className="col-md-10"></div>
					<div className="col-md-2">
						<Link to={config.url.api+"add-item"}>Create Item</Link>
					</div>
				</div><br />

				<table className="table table-hover">
					<thead>
						<tr>
							<td>ID</td>
							<td>Item Name</td>
							<td>Item Price</td>
							<td>Actions</td>
						</tr>
					</thead>
					<tbody>
						{this.tabRow()}
					</tbody>
				</table>
			</div>
		)
	}
}
export default DisplayItem;