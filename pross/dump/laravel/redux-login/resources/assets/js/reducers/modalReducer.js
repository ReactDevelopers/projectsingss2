const initialState = {
	currentModal : null
}

export default (state = initialState, action = {}) => {
	console.log('action.type');
	console.log(action.type);
	switch(action.type) {
		case 'SET_MODAL':
			return {
				currentModal: action.modal
			}

		default: return state;
	}
}
