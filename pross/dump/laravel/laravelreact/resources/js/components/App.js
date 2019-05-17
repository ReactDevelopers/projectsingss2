import React, { Component } from 'react'
import ReactDOM from 'react-dom'
import { BrowserRouter, Route, Switch } from 'react-router-dom'
import Header from './Header'
import NewProject from './NewProject'
import Register from './Register'
import Weather from './Weather'
import googlemap from './googlemap'

class App extends Component {
  render () {
    return (
      <BrowserRouter>
        <div>
          <Header />
          <Switch>
            <Route path='/create' component={NewProject} />
            <Route path='/register' component={Register} />
            <Route path='/weather' component={Weather} />
            <Route path='/googlemap' component={googlemap} />
          </Switch>
        </div>
      </BrowserRouter>
    )
  }
}

ReactDOM.render(<App />, document.getElementById('app'))