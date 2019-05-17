import React from 'react';

const textArea = ( {

field: {...fields},
        form: {touched, errors},
        ...props
    }) => (

    <div className="form-group">
       
        <textarea rows="6" cols="50" className="form-control b-r-0"  {...props} {...fields} invalid={Boolean(touched[fields.name] && errors[fields.name])}></textarea>
        {touched[fields.name] && errors[fields.name] ? <div className="error-message"><label className="c-pink">{errors[fields.name]}</label></div> : ''}
    </div>

);

export default textArea;
