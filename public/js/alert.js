const showAlertDelete = () =>  {
	return swal({
		title: 'Are you sure?',
		text: "Once deleted, you will not be able to recover this data!",
		icon: 'warning',
        buttons: true,
        dangerMode: true,
	}).then(result => result)
}

const showPopupConfirmation = (text, title) =>  {
	return swal({
		title: title,
		// text: "Apakah Anda Yakin Menghapus Data Ini?",
		text: text,
		icon: 'warning',
		confirmButtonText: 'Yes',
        buttons: true,
        dangerMode: true,
	}).then(result => result)
}

const showFailedAlert = (msg) => {
	swal({
		title: "Failed",
		text: msg,
		showConfirmButton: true,
		confirmButtonColor: '#0760ef',
		icon: "error"
	});
}

const showSuccessAlert = (msg) => {
	swal({
		title: "Success",
		text: msg,
		showConfirmButton: false,
		icon: "success",
		timer: 1500
	})
}


const showAlertOnSubmit = (params, modal, table, reload) => {
	if(params.status){
		setTimeout(function() {
			swal({
				title: "Success",
				text: params.message,
                button: false,
				// showConfirmButton: false,
				icon: "success",
				timer: 1500
			}).then(() => {
                // console.log('masuk sini lahh');
				if (modal) {
					$(modal).modal('hide');
				}
				if (table) {
					$(table).DataTable().ajax.reload( null, false );
				}
                // console.log('harusnya kensini : ', reload);
				if (reload) {
                    // console.log('yaudah replace kemana : ', reload);
					window.location.replace(reload);
				}
			});
		}, 200);
	} else {
		showFailedAlert(params.message);
	}
}


const setLoading = (message = '') => {
	swal({
		title: "Loading",
		// html: message,
        allowOutsideClick: false,
        allowEscapeKey: false,
		onOpen: () => {
            swal.showLoading();
        }
	});
}



