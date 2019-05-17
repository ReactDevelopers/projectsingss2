import React, { Component } from 'react';
import logo from './logo.svg';
import {Provider} from 'react-redux'
import './App.css';
import {connect} from 'react-redux'
import store from './store' 
import Test from './test' 

class App extends Component {
  render() {
    return (
      <div>
        <Provider store={store}>
          <Test/>
        </Provider>
      </div>
    
    );
  }
}


export default App;
