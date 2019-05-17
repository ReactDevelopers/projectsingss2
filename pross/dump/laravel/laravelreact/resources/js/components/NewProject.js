import axios from 'axios'
import React, { Component } from 'react'

class NewProject extends Component {
  constructor (props) {
    super(props)

    this.state = {
      email: '',
      password: '',
      errors: []
    }

    this.handleFieldChange = this.handleFieldChange.bind(this)
    this.handleCreateNewProject = this.handleCreateNewProject.bind(this)
    this.hasErrorFor = this.hasErrorFor.bind(this)
    this.renderErrorFor = this.renderErrorFor.bind(this)
  }

  handleFieldChange (event) {
    this.setState({
      [event.target.name]: event.target.value
    })
  }

  handleCreateNewProject (event) {
    event.preventDefault()

    const { history } = this.props

    const project = {
      email: this.state.email,
      password: this.state.password
    }
    // console.log('######');
    // console.log(project);
    axios
      .post('/api/projects', project)
      .then(response => {
        console.log('######');
        console.log(response.data[0]);
        if(response.data[0]=="success"){
          history.push('/')
        }
        // redirect to the homepage
        // history.push('/')
      })
      .catch(error => {
        this.setState({
          errors: error.response.data.errors
        })
      })
  }

  hasErrorFor (field) {
    return !!this.state.errors[field]
  }

  renderErrorFor (field) {
    if (this.hasErrorFor(field)) {
      return (
        <span className='invalid-feedback'>
          <strong>{this.state.errors[field][0]}</strong>
        </span>
      )
    }
  }

  render () {
    return (
      <div className='container py-4'>
        <div className='row justify-content-center'>
          <div className='col-md-6'>
            <div className='card'>
              <div className='card-header'>Please sign in</div>

              <div className='card-body'>

                <form onSubmit={this.handleCreateNewProject}>

                  <div className='form-group'>
                    <label htmlFor='name'>Email</label>
                    <input
                      id='email'
                      type='text'
                      className={`form-control ${this.hasErrorFor('email') ? 'is-invalid' : ''}`}
                      name='email'
                      value={this.state.email}
                      onChange={this.handleFieldChange}
                    />
                    {this.renderErrorFor('email')}
                  </div>

                  <div className='form-group'>
                    <label htmlFor='password'>Password</label>
                    <input
                      id='password'
                      type='password'
                      className={`form-control ${this.hasErrorFor('password') ? 'is-invalid' : ''}`}
                      name='password'
                      value={this.state.password}
                      onChange={this.handleFieldChange}
                    />
                    {this.renderErrorFor('password')}
                  </div>

                  <button className='btn btn-primary'>Create</button>
                </form>

              </div>
            </div>
          </div>
        </div>
      </div>
    )
  }
}

export default NewProject
