import { 
    Field, 
    Fields, 
    reduxForm, 
    ValidateCallback, 
    ConfigProps,  
    getFormSubmitErrors, 
    InjectedFormProps, 
    FormErrors, 
    FormAction, 
    FieldsProps, 
    WrappedFieldProps } from 'redux-form';

import * as React from 'react';
// import CKEditor, { CKEditorBase } from 'react-ckeditor-component';
// Require Editor JS files.


// Require Font Awesome.
// import 'font-awesome/css/font-awesome.css';
// import 'froala-editor/js/froala_editor.pkgd.min.js';

// Require Editor CSS files.
// import 'froala-editor/css/froala_style.min.css';
// import 'froala-editor/css/froala_editor.pkgd.min.css';
// import FroalaEditor from 'react-froala-wysiwyg';



import ReactSummernote from 'react-summernote';
import 'react-summernote/dist/react-summernote.css'; // import styles
//import 'react-summernote/lang/summernote-ru-RU'; // you can import any other locale
import 'bootstrap/js/modal';
import 'bootstrap/js/dropdown';
import 'bootstrap/js/tooltip';
import 'bootstrap/dist/css/bootstrap.css';


import {FieldGeneralProps} from '../FormInit';

export type WrappedRichTextFieldProps = {
    // label?: string,
    // className?: string,
    // elementclassName?: string,
    placeholder?: string,
    serverError?: string | undefined,
} & WrappedFieldProps & FieldGeneralProps;

/**
 * HTML Text Element With the Error Message
 * @param param0 
 */
class  DateInput extends React.Component<WrappedRichTextFieldProps>{


    constructor(props: WrappedRichTextFieldProps) {

        super(props);
        this.onChange = this.onChange.bind(this);
    }


    onChange(data: string) {

        this.props.input.onChange(data);
    }
    render() {
        
        const {input, placeholder, serverError, meta: { touched , error, submitting}  } = this.props;
        const hasError = !!error && !!touched || !!serverError;      
        
        return (
        <>
        <ReactSummernote
        value={input.value}
        options={{
          //lang: 'en',
          //height: 350,
          dialogsInBody: true,
          placeholder:placeholder,
        //   fontNamesIgnoreCheck:['Open Sans'],
          fontNames: ['Arial', 'Arial Black','calibri', 'Comic Sans MS', 'Courier New','Open Sans'],
          toolbar: [
            ['style', ['style']],
            ['color', ['color']],
            ['font', ['bold', 'underline', 'clear','italic','subscript','superscript','strikethrough']],
            ['fontname', ['fontname']],
            ['fontsize', ['fontsize']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link']],
            ['view', ['fullscreen', 'codeview','undo','redo']],           
              
          ]
        }}
        onChange={this.onChange}
      />
            {/* <FroalaEditor 
            tag='textarea'
            model={input.value}
            onModelChange={this.onChange}
            config={{
                placeholderText:placeholder,
                imageUpload: false,
                toolbarButtons: [
                    'fullscreen', 
                    'bold', 
                    'italic', 
                    'underline', 
                    'strikeThrough', 
                    'subscript', 
                    'superscript', 
                    '|', 
                    'fontFamily', 
                    'fontSize', 
                    'color', 'inlineStyle', 'paragraphStyle', '|', 
                    'paragraphFormat', 'align', 'formatOL', 'formatUL', 
                    'outdent', 'indent', 
                    'quote', '-', 
                    'insertLink', 
                    //'insertImage', 
                    //'insertVideo', 
                    //'embedly', 'insertFile', 
                    'insertTable', '|', 'emoticons', 
                    'specialCharacters', 
                    'insertHR', 
                    'selectAll', 
                    'clearFormatting', '|', 'print', 
                    'spellChecker', 'help', 
                    'html', '|', 'undo', 'redo'
                ]
            }}
            /> */}
            {hasError && <span className="help-block">{serverError? serverError : error }</span>}
        </>
        );
    }

}
export default DateInput; 