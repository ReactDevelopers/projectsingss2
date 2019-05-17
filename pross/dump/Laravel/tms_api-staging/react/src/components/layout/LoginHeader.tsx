import * as React from 'react';
import { Row, Col } from 'react-bootstrap';
var logo =  require('../../scss/assets/images/logo_tms.png');
import  {Link} from 'react-router-dom';

export default class LoginHeader extends React.Component {

    shouldComponentUpdate() {
        return false;
    }
    
    render() {

        return (
            <div className="navbar">
                <div className="container">
                    <Row className="col">
                        <Col md={6} sm={6} xs={12}>
                            <div className="navbar-header">
                                <Link to="/" className="navbar-brand">
                                    <img src={logo} alt="TMS Logo" />
                                </Link>
                            </div>
                        </Col>
                        <Col md={6} sm={6} xs={12}>
                            <div className="header-message">
                                <div className="right-content">
                                    <p>PUB, Singapore’s national water agency.</p>
                                    <p className="sub-head">Managing the country’s water supply,</p>
                                    <p className="sub-head">water catchment and used water in an integrated way.</p>
                                </div>
                            </div>
                        </Col>
                    </Row>
                </div>
            </div>
        )
    }
}