import * as React from 'react';
import { Button } from 'react-bootstrap';
import { NavLink } from 'react-router-dom';
var FAS = require('react-fontawesome');

interface DownloadBtnProps {

    filename: Array<{name: string, title: string}>;
}

export default class DownloadBtn extends React.Component<DownloadBtnProps> {

    shouldComponentUpdate() {return false}
    
    render () {
        const { filename } = this.props;

        return (
            <div className="text-right">
            {filename.map(f => {
            
               return (

                    <span key={`download_file${f.name}`}  className="info drop-link" >
                        <a key={`download_file${f.name}`} title={f.title}   href={`${process.env.DOMAIN+process.env.DOMAIN_PATH}sample/${f.name}`} 
                        className="download-btn">
                            <FAS name="download" />
                        </a>               
                    </span> 
               )
            })}
            </div>
        );
    }
}