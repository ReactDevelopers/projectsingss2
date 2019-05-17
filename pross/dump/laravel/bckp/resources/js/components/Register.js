import axios from 'axios'
import React, { Component } from 'react'
import swal from 'sweetalert'

// Import React FilePond
import { FilePond, registerPlugin } from "react-filepond";

// Import FilePond styles
import "filepond/dist/filepond.min.css";

// Import the Image EXIF Orientation and Image Preview plugins
// Note: These need to be installed separately
import FilePondPluginImageExifOrientation from "filepond-plugin-image-exif-orientation";
import FilePondPluginImagePreview from "filepond-plugin-image-preview";
import "filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css";

// Register the plugins
registerPlugin(FilePondPluginImageExifOrientation, FilePondPluginImagePreview);

class Register extends Component {
  constructor (props) {
    super(props)

    this.state = {
      name: '',
      email: '',
      password: '',
      password_confirmation: '',
      errors: [],
      files: '',
      currentFile:[]
    }

    this.handleFieldChange = this.handleFieldChange.bind(this)
    this.handleCreateNewProject = this.handleCreateNewProject.bind(this)
    this.hasErrorFor = this.hasErrorFor.bind(this)
    this.renderErrorFor = this.renderErrorFor.bind(this)
  }

  handleInit() {
    console.log("FilePond instance has initialised", this.pond);
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
      name: this.state.name,
      email: this.state.email,
      password: this.state.password,
      password_confirmation: this.state.password_confirmation,
      currentFile: this.state.currentFile
    }
    console.log('######');
    console.log(project);
    axios
      .post('/api/register', project, {
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'multipart/form-data'
    }})
      .then(response => {
        console.log('3333');
        console.log(response);
        if(response.data[0]=="success"){
          this.setState({
            name: '',
            email: '',
            password: '',
            password_confirmation: '',
            errors: []
          });
          swal({
                title: "User Register Successfully!",
                icon: "success",
                button: "Ok",
              })
          // history.push('/')
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

  onProcessFile (err, fileItem) {
    console.log("onprocessfile");
    console.log(fileItem.source);
    this.setState({
      currentFile: {
        source: fileItem.source,
        options: {
          type: "local"
        }
      }
    });
  };

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
          <div className='col-md-4'>
            <div className='card'>
              <div className='card-header'>Sign Up</div>

              <div className='card-body'>

                <form onSubmit={this.handleCreateNewProject}>

                  <div className='form-group'>
                    <label htmlFor='name'>Name</label>
                    <input
                      id='name'
                      type='text'
                      placeholder="Name"
                      className={`form-control ${this.hasErrorFor('name') ? 'is-invalid' : ''}`}
                      name='name'
                      value={this.state.name}
                      onChange={this.handleFieldChange}
                    />
                    {this.renderErrorFor('name')}
                  </div>

                  <div className='form-group'>
                    <label htmlFor='name'>Email</label>
                    <input
                      id='email'
                      type='text'
                      placeholder="Email"
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
                      placeholder="Password"
                      className={`form-control ${this.hasErrorFor('password') ? 'is-invalid' : ''}`}
                      name='password'
                      value={this.state.password}
                      onChange={this.handleFieldChange}
                    />
                    {this.renderErrorFor('password')}
                  </div>

                  <div className='form-group'>
                    <label htmlFor='password_confirmation'>Confirm Password</label>
                    <input
                      id='password_confirmation'
                      type='password'
                      placeholder="Confirm Password"
                      className={`form-control ${this.hasErrorFor('password_confirmation') ? 'is-invalid' : ''}`}
                      name='password_confirmation'
                      value={this.state.password_confirmation}
                      onChange={this.handleFieldChange}
                    />
                    {this.renderErrorFor('password_confirmation')}
                  </div>

                  <FilePond
                    ref={ref => (this.pond = ref)}
                    files={this.state.files}
                    allowMultiple={true}
                    maxFiles={3}
                    name={"file"}
                    server="/api/upload"
                    oninit={() => this.handleInit()}
                    onprocessfile={this.onProcessFile.bind(this)}
                    onupdatefiles={fileItems => {
                      console.log('hello');
                      console.log(fileItems);
                      // Set currently active file objects to this.state
                      this.setState({
                        files: fileItems.map(fileItem => fileItem.file)
                      });
                    }}
                  >
                  {this.props.currentFile && (
                    <File key={this.props.currentFile} src={this.props.currentFile} origin="local" />
                )}
                  </FilePond>
                  <button className='btn btn-primary'>Sign Up</button>
                </form>

              </div>
            </div>
          </div>
        </div>
      </div>
    )
  }
}

export default Register
