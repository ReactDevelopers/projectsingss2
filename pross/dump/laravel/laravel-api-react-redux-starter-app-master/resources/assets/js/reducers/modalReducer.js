const initialState = {
	currentModal : null
}

export default (state = initialState, action = {}) => {
	switch(action.type) {
		case 'SET_MODAL':
			return {
				currentModal: action.modal
			}

		default: return state;
	}
}
