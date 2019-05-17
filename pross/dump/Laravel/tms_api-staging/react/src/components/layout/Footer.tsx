import * as React from 'react';
import * as bs from 'react-bootstrap';

export default class Footer extends React.Component {

    shouldComponentUpdate() {

        return false;
    }
    
    render() {

        const date  = new Date();
        const year = date.getFullYear();
        //const copyrightMessage = 'Created By: Wong Teng Kuan (HR), Calister Hoh (HR), Saiful Shahril Saini (IS Dept).';
        const copyrightMessage  ='';
        return (
            <div className="footer_wrap">
            <bs.Grid>
                <bs.Row>
                    <bs.Col sm={4}><p className="copyrights_para left">© {year} PUB. All rights reserved.</p></bs.Col>
                    <bs.Col sm={8}>
                        <p  className="copyrights_para right">{copyrightMessage}</p>
                    </bs.Col>
                </bs.Row>                   
            </bs.Grid>
            </div>
        );
    }
}