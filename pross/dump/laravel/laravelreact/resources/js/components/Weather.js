import axios from 'axios'
import React, { Component } from 'react'
import {geolocated} from 'react-geolocated';

class Weather extends React.Component {

  constructor (props) {
    super(props)
    
    this.state = {
      lat: '',
      lon: '',
      currentFile:[],
      data:[]
    }
  }

  componentDidUpdate(prevProps, prevState) {
    // console.log('new#########');

    // console.log(prevProps);
    // console.log('old#########');

    // console.log(prevState);
    // console.log('part#########');

    // console.log(this.props.coords);

    if (prevState.lat=='') {
      this.setState({
        lat: this.props.coords.latitude,
        lon: this.props.coords.longitude
      });
    }

    const weather = {
      latitude: this.props.coords.latitude,
      longitude: this.props.coords.longitude
    }
    // console.log('weather send');
    // console.log(weather);
    axios
    .post('/api/weather', weather)
    .then(response => {
      // console.log('######');
      // console.log(response.data.display);
      // console.log('zero######');
      // // console.log(data.data.display.main);

      this.setState({
          data: response.data.display
      });
      // console.log('wehhhe soft');
      // console.log(this.state.data);
      // console.log('weather done');
    })
    .catch(error => {
      console.log(error.response.data.errors);
    })
  }

   render() {
    const { data } = this.state;
    return !this.props.isGeolocationAvailable
      ? <div>Your browser does not support Geolocation</div>
      : !this.props.isGeolocationEnabled
        ? <div>Geolocation is not enabled</div>
        : this.props.coords
          ? <table>
            <tbody>
              <tr><td>latitude</td><td>{this.props.coords.latitude}</td></tr>
              <tr><td>longitude</td><td>{this.props.coords.longitude}</td></tr>
              <tr><td>pressure</td><td>{this.state.data.pressure?this.state.data.pressure:'is loading...'}</td></tr>
              <tr><td>Temperature min</td><td>{this.state.data.temp_min?this.state.data.temp_min:'is loading...'}</td></tr>
              <tr><td>Temperature max</td><td>{this.state.data.temp_max?this.state.data.temp_max:'is loading...'}</td></tr>
              <tr><td>Humidity</td><td>{this.state.data.humidity?this.state.data.humidity:'is loading...'}</td></tr>
              <tr><td>Temperature</td><td>{this.state.data.temp?this.state.data.temp:'is loading...'}</td></tr>
            </tbody>
          </table>
          : <div>Getting the location data&hellip; </div>;

  
  }
}
 
export default geolocated({
  positionOptions: {
    enableHighAccuracy: false,
  },
  userDecisionTimeout: 5000,
})(Weather);
