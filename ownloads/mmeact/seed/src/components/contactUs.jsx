import React from 'react';
import { render } from 'react-dom';
import { Formik , Field, Form} from "formik";
import * as Yup from 'yup';
import { ReCaptcha } from 'react-recaptcha-google'
import axios from 'axios'
import Header from '../layouts/header'
import * as config from '../constant/config'
import Footer from '../layouts/footer'
import GoogleMapReact from 'google-map-react';
import inputField from '../components/InputField/textField'
import textField from '../components/InputField/textarea'
import MobileHeader from '../layouts/mobileHeader'
import Select from 'react-select';
import swal from 'sweetalert';
import {NavLink} from 'react-router-dom'
import shareIcon from '../assets/images/share-icon.png'



const options = [

    { value: 1 , label: '1' },
    { value: 2 , label: '2' },

];
  


const DummyComponent = ({  }) => <div>{}</div>;

export default class contactForm extends React.Component { 



     
   
      

      constructor(props){

        super(props)
        
        this.state = {
           termsStatus : '',
           selectedOption: 1,
           recaptchaToken : ''
        }

      }


      static defaultProps = {
            center: {
                lat: 59.95,
                lng: 30.33
            },
            zoom: 11
      };


    // changeState(){
    //       this.setState({
    //           termsStatus : ''
    //       })
    //   }

  componentDidMount() {
        const script = document.createElement("script");
        script.src =
          "https://www.google.com/recaptcha/api.js";
        script.async = true;
        script.defer = true;
        document.body.appendChild(script);

        
        if (this.captchaDemo) {
            console.log("started, just a second...")
            this.captchaDemo.reset();
            this.captchaDemo.execute();
        }
    }



    handleChange = (selectedOption) => {
        this.setState({ selectedOption });
        console.log(`Option selected:`, selectedOption);
    }
  
    onLoadRecaptcha() {
        if (this.captchaDemo) {
            this.captchaDemo.reset();
            this.captchaDemo.execute();
        }
    }

    verifyCallback(recaptchaToken) {
        // Here you will get the final recaptchaToken!!!  
        this.setState({
            recaptchaToken
        })
        console.log(recaptchaToken, "<= your recaptcha token");
    }

  render() {

    const { selectedOption } = this.state;

    const { recaptchaToken } = this.state;

    console.log("captcha key ", recaptchaToken)

    return (
        <section className="contact-us-section form-section">
        <MobileHeader/>
        <Header/>
        <div class="relative mobile-heading b-t-lightGray">
            <div class="d-flex flex-v-center flex-sps-between">
                <h2 class="c-darkGray visible-xs hidden">Contact Us</h2>
                <NavLink to="/" class="share-icon active visible-xs" aria-current="page">
                    <img src={shareIcon}/>
                </NavLink>
            </div>
        </div>
        <section class="banner-section contactus-section"><div class="banner-image about-us-banner"></div></section>
        <div className="sections-wrapper contact-us-wrapper bg-Gray">
          <div className="container">
                <div class="d-flex flex-v-center flex-sps-between hidden-xs">
                    <h2 class="c-darkGray heading">Contact Us</h2>
                    <NavLink to="/" class="share-icon active" aria-current="page">
                        <img src={shareIcon} alt="share"/>
                    </NavLink>
                </div>
                <div className="map-wrapper" style={{  width: '100%' }}>
                    <div className="common-block  p-b-40 responsive-address-head w-100">
                        <h4 className="fontWeightB p-b-20 icon-image address hidden visible-xs">Address</h4>
                    </div>
                      <GoogleMapReact
                        defaultCenter={this.props.center}
                        defaultZoom={this.props.zoom}
                      >
                      <DummyComponent
                          lat={59.955413}
                          lng={30.337844}
                          text="My Marker"
                      />
                      </GoogleMapReact>

                      <div className="map-info static-content space-around bg-white">
                          <div className="row">
                              <div className="col-md-6 col-sm-6 col-xs-12">
                                  <div className="common-block  p-b-20 responsive-address">
                                      <h4 className="fontWeightB p-b-20 icon-image address">Address</h4>
                                      <p>Mountbatten Square 229 Mountbatten Road #02-42 Singapore 398007</p>
                                  </div>
                                  <div className="common-block contact">
                                      <h4 className="fontWeightB p-b-20 contact icon-image">Contact</h4>
                                      <p>https://www.facebook.com/seedinst/</p>
                                  </div>
                              </div>
                              <div className="col-md-6 col-sm-6 col-xs-12">
                                  <div className="common-block">
                                        <h4 className="fontWeightB p-b-20 icon-image admin-block ">Administration Office</h4>
                                        <div className="block p-b-20">
                                            <h4 className="c-pink f-bold">Monday to Friday: 9am - 6pm</h4>
                                            <p>For course consultation/ application, last appointment by 5pm</p>
                                        </div>
                                        <div className="block p-b-20">
                                            <h4  className="c-pink f-bold">Saturday: 10am - 2pm </h4>
                                            <p> On 2nd and 4th Saturdays of every month For course consulation/ application, last appointment by 1pm</p>
                                        </div>
                                        <div className="block">
                                            <h4  className="c-pink f-bold">Closed on Sunday and Public Holidays.</h4 >
                                        </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
                  <div className="static-content space-around bg-white form-wrapper-box clear">
                        <h2 className="c-darkGray heading main">General Enquiry</h2>
                        
                  <Formik
                    initialValues={{
                          email: "",
                          name: "",
                          contact_no : "",
                          subject : "",
                          enquiry : "",
                          mode_of_contact : 1,
                          termsname : false
                      }}

                      onSubmit={(values) => {


                            var checkedData = {
                              "termsname" : values.termsname
                            }
                            console.log(checkedData.termsname);
                            console.log(values);
                            if(checkedData.termsname == false){
                                swal({
                                    title: "Please accept Terms and Conditon !",
                                    icon: "warning",
                                    buttons: true,
                                    dangerMode: true,
                                  })
                            }

                            else {
                                var data = {
                                    "email" : values.email,
                                    "name" : values.name,
                                    "contact_no" : values.contact_no,
                                    "subject" : values.subject,
                                    "mode_of_contact" : this.state.selectedOption.value,
                                    "enquiry" : values.enquiry,
                                }
                            console.log(data);
                            console.log(this.state.recaptchaToken);
                                axios({
                                    method: 'post',
                                        url: config.API_URL+'enquiry',
                                        data: data,
                                        headers : {
                                        'Content-Type': 'application/json',
                                        'X-RECAPTCHA-RESPONSE' : this.state.recaptchaToken 
                                    }
                                 })
                                .then(function (response) {
                                    swal({
                                        title: "Contact Form submitted!",
                                        icon: "success",
                                        button: "Ok",
                                      })
                                })
                                .catch(function (response) {
                                    swal({
                                        title: "Contact Form not Submitted!",
                                        icon: "error",
                                        button: "Ok",
                                      })
                                });
                                }                  
                            }
                        }

                        validationSchema= {Yup.object().shape({
                            name: Yup.string().required(),
                            email: Yup.string().email().required(),
                            email: Yup.string().email().required(),
                        })}

                      render={({  errors, touched, handleSubmit, setFieldValue }) => (

                        
                        <form onSubmit={handleSubmit}>
                            <div className="row">

                              <div className="col-md-6 col-sm-6 col-xs-12">
                                  <label  className={"label-color"}>*Name</label>
                                  <Field type="text" label="Name" name="name" component={inputField}/>
                              </div>

                              <div className="col-md-6 col-sm-6 col-xs-12">
                                  <label  className={"label-color"}>*Email</label>
                                  <Field type="email" label="Email" name="email" component={inputField}/>
                              </div>

                              <div className="col-md-6 col-sm-6 col-xs-12"> 
                                 <label  className={"label-color"}>*Contact Number</label>
                                  <Field type="text" label="Contact Number" name="contact_no" component={inputField}/>
                              </div>

                              <div className="col-md-6 col-sm-6 col-xs-12">
                                <label>*Preferred mode of contact</label>
                                <div className="form-group">
                                    <Select
                                        value={selectedOption}
                                        onChange={this.handleChange}
                                        options={options}
                                    />
                                </div>
                              </div>

                              <div className="col-md-12 col-sm-12 col-xs-12">
                                  <label  className={"label-color"}>Subject</label>
                                  <Field type="text" label="Subject" name="subject" component={inputField}/>
                              </div>

                              <div className="col-md-12 col-sm-12 col-xs-12">
                                <label  className={"label-color"}>Enquiry</label>
                                  <Field type="text" label="Enquiry" name="enquiry" component={textField}/>
                              </div>

                              <div className="col-md-12 col-sm-12 col-xs-12">
                                <div className="accept-condition p-t-30">
                                    <div class="checkboxBtn contactus-checkbox">
                                                <Field type="checkbox" id="checkbox02" name="termsname"/>
                                                <label for="checkbox02"><span></span><h4 className="declaration">Declaration</h4><a class="termscondition fontmedium" href="javascript:void(0)">
                                                I acknowledge and agree that SEED Institute Pte Ltd may collect, use and disclose to any third party any and all particulars relating to my personal information for the purposes of (i) providing early childhood & care related training and associated services, (ii) billing and account management (including debt collection or recovery); (iii) conducting surveys or obtaining feedback; (iv) informing me of services and offers by SEED Institute Pte Ltd, its related entities and business affiliates (unless I duly inform you otherwise); and (v) complying with all applicable laws and regulations, and business requirements.<br/> 
                                            </a>
                                            </label>
                                            <span className="detail-link">For details of our Privacy Policy, please follow this link: <a class="c-pink f-bold" href="http://www.seedinstitute.edu.sg">http://www.seedinstitute.edu.sg</a></span>
                                            <div className="terms-status">
                                                {this.state.termsStatus}
                                            </div>
                                      
                                      </div>
                                      <div className="form-group">

                                             <ReCaptcha
                                                ref={(el) => {this.captchaDemo = el;}}
                                                size="invisible"
                                                render="explicit"
                                                sitekey="6LdE4oMUAAAAAC3GVjaOKnodk3eFeUSFDI_REq3d"
                                                onloadCallback={this.onLoadRecaptcha.bind(this)}
                                                verifyCallback={this.verifyCallback.bind(this)}
                                            />


                                          {/* <Recaptcha
                                              sitekey="6LdE4oMUAAAAAC3GVjaOKnodk3eFeUSFDI_REq3d"
                                              render="explicit"
                                              verifyCallback={(response) => { setFieldValue("recaptcha", response); }}
                                              onloadCallback={() => { console.log("done loading!"); }}
                                          /> */}

                                    </div>
                                    <div className="submit-button">
                                        <button type="submit" className="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                            </div>
                      </div>
                     
                    </form>
                  )} />
            </div>
          
            </div>
        </div>
            <Footer/>
      </section>
    );
  }
};

