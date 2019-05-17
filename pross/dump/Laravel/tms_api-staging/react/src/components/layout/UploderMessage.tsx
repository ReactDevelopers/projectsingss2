import * as React from 'react'; 

interface MessageProps {
    message: string;
}
export default class Message extends React.Component<MessageProps> {

    render() {
        const {message} = this.props;
        return (
            <div className="dropzone-message-wrap white-overlay">
                <h4> <span>{message}</span> (or click here to browser dialog)</h4>
                <p>Please only upload the file with the XLSX or XLS extension.</p>
            </div>
        );
    }
}