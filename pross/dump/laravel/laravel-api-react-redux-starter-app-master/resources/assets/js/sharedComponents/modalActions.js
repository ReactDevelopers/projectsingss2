export function setModal(modal = '') {
	return dispatch => {
		if (modal) {
			document.querySelector('body').classList.add('modal-open');
		} else {
			document.querySelector('body').classList.remove('modal-open');
		}
		dispatch({
			type: 'SET_MODAL',
			modal: modal
		});
	}
}
