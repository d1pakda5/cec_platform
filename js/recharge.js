// JavaScript Document
$(document).ready(function(){
	$('#mobileForm').validate({
		submitHandler : function(form) {
			if(confirm("Are you sure want to continue?")) {
        form.submit();
      }
		},
	  rules: {
	    operator: {
	    	required: true
	    },
			account: {
				required: true,
				digits: true,
				minlength: 10,
				maxlength: 10
			},
			amount: {
				required: true,
				digits: true
			},
			pin: {
				required: true,
				digits: true,
	      minlength: 4,
				maxlength: 4
	    }
	  },
		highlight: function(element) {
			$(element).closest('.jrequired').addClass('text-red');
		}
	});
	
	$('#dthForm').validate({
		submitHandler : function(form) {
			if(confirm("Are you sure want to continue?")) {
      	form.submit();
      }
		},
	  rules: {
	  	operator: {
				required: true
			},
			account: {
				required: true,
				digits: true,
				minlength: 6
			},
			amount: {
				required: true,
				digits: true,
				minlength: 2
			},
			pin: {
				required: true,
				digits: true,
				minlength: 4,
				maxlength: 4
	    }
	  },
		highlight: function(element) {
			$(element).closest('.jrequired').addClass('text-red');
		}
	});
	
	$('#dataForm').validate({
		submitHandler : function(form) {
			if(confirm("Are you sure want to continue?")) {
        form.submit();
      }
		},
		rules: {
			operator: {
				required: true
			},
			account: {
				required: true,
				digits: true,
				minlength: 10,
				maxlength: 10
			},
			amount: {
				required: true,
				digits: true,
				minlength: 2
			},
			pin: {
				required: true,
				digits: true,
				minlength: 4,
				maxlength: 4
			}
		},
		highlight: function(element) {
			$(element).closest('.jrequired').addClass('text-red');
		}
	});
	
	$('#postpaidForm').validate({
		submitHandler : function(form) {
			if(confirm("Are you sure want to continue?")) {
        form.submit();
      }
		},
		rules: {
			operator: {
				required: true
			},
			account: {
				required: true,
				digits: true,
				minlength: 10,
				maxlength: 10
			},
			amount: {
				required: true,
				digits: true,
				minlength: 2
			},
			pin: {
				required: true,
				digits: true,
				minlength: 4,
				maxlength: 4
			}
		},
		highlight: function(element) {
			$(element).closest('.jrequired').addClass('text-red');
		}
	});
	
	$('#landlineForm').validate({
		submitHandler : function(form) {
			if (confirm("Are you sure want to continue?")) {
        form.submit();
      }
		},
		rules: {
			operator: {
				required: true
			},
			account: {
				required: true,
				digits: true
			},
			amount: {
				required: true,
				digits: true,
				minlength: 2
			},
			pin: {
				required: true,
				digits: true,
				minlength: 4,
				maxlength: 4
			}
		},
		highlight: function(element) {
			$(element).closest('.jrequired').addClass('text-red');
		}
	});
	
	$('#electricityForm').validate({
		submitHandler : function(form) {
			if (confirm("Are you sure want to continue?")) {
        	form.submit();
      	}
			},
	    rules: {
	    	operator: {
	        required: true
	      },
				account: {
					required: true
				},
				amount: {
					required: true,
					digits: true,
					minlength: 2
				},
				pin: {
					required: true,
					digits: true,
					minlength: 4,
					maxlength: 4
				}
	    },
		highlight: function(element) {
			$(element).closest('.jrequired').addClass('text-red');
		}
	});
	
	$('#gasForm').validate({
		submitHandler : function(form) {
			if(confirm("Are you sure want to continue?")) {
        form.submit();
      }
		},
		rules: {
			operator: {
				required: true
			},
			account: {
				required: true
			},
			amount: {
				required: true,
				digits: true,
				minlength: 2
			},
			pin: {
				required: true,
				digits: true,
				minlength: 4,
				maxlength: 4
			}
		},
		highlight: function(element) {
			$(element).closest('.jrequired').addClass('text-red');
		}
	});
	
	$('#insuranceForm').validate({
		submitHandler : function(form) {
			if (confirm("Are you sure want to continue?")) {
        form.submit();
      }
		},
		rules: {
			operator: {
				required: true
			},
			account: {
				required: true
			},
			dob: {
				required: true
			},
			amount: {
				required: true,
				digits: true,
				minlength: 2
			},
			pin: {
				required: true,
				digits: true,
				minlength: 4,
				maxlength: 4
			}
		},
		highlight: function(element) {
			$(element).closest('.jrequired').addClass('text-red');
		}
	});
});