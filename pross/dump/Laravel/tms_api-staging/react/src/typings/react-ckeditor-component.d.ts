
declare module "react-ckeditor-component" {

	import React = require("react");

	interface CKEditorBase {

		editorInstance: any;
	}

	export default class ReactCkEditor extends React.Component<any, any> implements CKEditorBase {

		editorInstance: any;
	}
}