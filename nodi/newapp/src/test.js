import React,{ Component } from "react";
import store from './store' 
import {Provider} from 'react-redux'
import {connect} from 'react-redux'
import {increment} from './actions/action'

import {decrement} from './actions/action'
 
class test extends Component {

	constructor(){
		super()
	}

	plusBind(){
		this.props.dispatch(increment())
	}

	minusBind(){
		this.props.dispatch(decrement())
	}

  render() {

    return (
     
		<div>
		    <div className="App spanlisting">
		    <span onClick={this.plusBind.bind(this)}>+</span>
		    <span>{this.props.count}</span>
		    <span onClick={this.minusBind.bind(this)}>-</span>
		  </div>  
		</div>

    );
  }
}
 
function mapStateToProps(state){
  return {
    ...state
  }
}

export default connect(mapStateToProps)(test);
