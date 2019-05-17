import {Props, mapDispatchToProps, mapStateToProps, ServerResponse} from '../../features/root-props';
console.log('....ssdsds');
import * as React from 'react';
import {connect} from 'react-redux';
import { Row, Col } from 'react-bootstrap';
import Footer from 'Components/layout/Footer';
import LoginHeader from 'Components/layout/LoginHeader';
import LoginForm , {LoginFormData, LoginFormProps} from './LoginForm';
import {SubmissionError, FormErrors } from 'redux-form';
import API from '../../aep';
import Message from '../layout/Message';
import * as actions from '../../features/root-action';
import {AuthI} from '../../models/Auth';
import Cookie from 'js-cookie';
import Loading from '../layout/LoaderBar';
import auth from '../../aep/auth';

const sleep = (ms: number) => new Promise(resolve => setTimeout(resolve, ms))

class Login extends React.Component<Props> {

    constructor(props: Props) {

        super(props);
        this.onSubmit = this.onSubmit.bind(this);
    }
    componentWillMount() {

        const auth_user = this.props.rootState.server['auth_user'];
        if(auth_user && auth_user.response && auth_user.response.data) {

            this.props.history.push('/dashboard');
        }
    }

    /**
     * Handling the Login Request..
     * @param value 
     * @param dispatch 
     * @param props 
     */
    onSubmit(value: LoginFormData, dispatch: any, props: LoginFormProps ) {
        
        const IsError = Object.keys(props.syncErrors).length;
        if(!IsError) {

            var auth_end_point = {...API.AUTH_USER};
            auth_end_point.extendResponse = (data: ServerResponse<AuthI>) => {
                
                data.data.view_as = (data.data.role_id === 1 || data.data.role_id == 3) ? 'Admin' : 'Viewer';   
                localStorage.setItem('tms_view_as', data.data.view_as);             
            };

            return this.props.callApi(auth_end_point, value)
                .then((response: ServerResponse<AuthI>) => {

                    this.props.callApi(API.GET_OPTIONS);

                    const auth_data: AuthI | null = response.data;

                    Cookie.set('access_token', auth_data? auth_data.token : '', {
                        expires: 356
                    });

                    this.props.history.push('/dashboard');

                }).catch((response: ServerResponse) => {
                    
                    const message = response.message ? response.message : '';
                    throw new SubmissionError({...response.errors, _error: message});
                })
        }
        else {

            throw new SubmissionError({...props.syncErrors, _error: 'Invalid data'});
        }
        
    }

    render() {
        const form = this.props.rootState.form['login'];
        const error = form ? form.error : undefined;
        const isError: boolean = error ? true: false;
        //console.log(this.props);
        return (
            <div className="site-wrapper main-wrapper loginPage">
                <Loading {...this.props} />
                <LoginHeader/>
                <div className="formContainer loginContainer">
                    <div className="container">
                        <div className="login-box">
                            <div className="login-box-inner">
                                <h3 className="login-heading text-center">Training MANAGEMENT SYSTEM</h3>
                                <h4 className="login-subheading text-center">Please log into your account</h4>
                                <div className="formWrapper">
                                    <LoginForm onSubmit={this.onSubmit}  />
                                </div>                                
                            </div>
                        </div>
                        <div className="login-error-message-container ">
                            <Message isError={isError} message={error} />
                        </div>
                    </div>
                </div>
                <Footer/>
            </div>
        )
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(Login);