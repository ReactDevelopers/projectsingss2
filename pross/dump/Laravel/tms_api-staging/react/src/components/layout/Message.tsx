import * as React from 'react';
import { Alert } from 'react-bootstrap';
let Parser = require('html-react-parser');

interface MessageProps {

    message: string | undefined;
    isError: boolean;
}

class Message extends React.Component<MessageProps> {

    constructor(props: MessageProps) {

        super(props);
        this.handleDismiss = this.handleDismiss.bind(this);
    }

    handleDismiss () {

        //store.dispatch(clearMessage());
    }

    public render() {   
        
        if (this.props.message) {

            return (
                    
             <Alert bsStyle={this.props.isError ? 'danger' : 'success'} >
                <p><strong>{this.props.isError ? 'Error!' : 'Success'}</strong> {Parser(this.props.message)}</p>
             </Alert>           
            );
        }
        return ('');

    }
}
export default Message;