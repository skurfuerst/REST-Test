$(document).ready(function(){

	var restRequest = function(uri, method, data, callback) {
		$('body').addClass('loading');
		$.ajax({
			url: uri,
			type: method,
			data: data,
			dataType: getAjaxMode(),
			success: callback,
			complete: function() {
				$('body').removeClass('loading');
			},
			error: function() {
				// TODO: something better than:
				alert('An error occurred');
			},
		});
	}

	var getAjaxMode = function() {
		return $('#ajaxMode').val();
	}

	var resetForm = function() {
		$('#productLabel').text('New product');
		$('#name').val('');
		$('#price').val('');
		$('#identity').val('');
		$('#uri').val('');
		$('#put').attr('disabled', 'disabled');
		$('#delete').attr('disabled', 'disabled');
	}

	var parseAjaxResult = function(response) {
		if (getAjaxMode() == 'xml') {
			var productNode = $(response).find('product');
			return {
				__identity: productNode.attr('__identity'),
				name: productNode.find('name').text(),
				price: parseFloat(productNode.find('price').text()),
				uri: productNode.find('link[rel=self]').attr('href')
			}
		} else {
			var product = response.product;
			product.uri = product.links[0].href;
			delete product.links;
			return product;
		}
	}

		// GET
	$('.get').click(function(event) {
		if (getAjaxMode() == '') {
			return;
		}
		event.preventDefault();
		restRequest($(this).attr('href'), 'GET', null, function(response) {
			var product = parseAjaxResult(response);
			$('#productLabel').text('"' + product.name + '" (' + product.uri + ')');
			$('#name').val(product.name);
			$('#price').val(product.price);
			$('#identity').val(product.__identity);
			$('#uri').val(product.uri);
			$('#put').removeAttr('disabled');
			$('#delete').removeAttr('disabled');
		});
	});

		// CREATE
	$('#post').click(function(event) {
		if (getAjaxMode() == '') {
			return;
		}
		event.preventDefault();
		var product = {
			name: $('#name').val(),
			price: $('#price').val()
		};
		if (product.name.length < 1) {
			alert('Please enter a product name!');
			return;
		}
		restRequest('/products', 'POST', {product: product}, function(response) {
			location.reload();
		});
	});

		// UPDATE
	$('#put').click(function(event) {
		event.preventDefault();
		if (getAjaxMode() == '') {
			alert('Request method PUT is not supported by standard forms');
			return;
		}
		var modifiedProduct = {
			__identity: $('#identity').val(),
			name: $('#name').val(),
			price: $('#price').val(),
		};
		restRequest($('#uri').val(), 'PUT', {product: modifiedProduct}, function(response) {
			$('[data-identifier=' + modifiedProduct.__identity + '] a').first().text(modifiedProduct.name);
			resetForm();
		});
	});

		// DELETE
	$('#delete').click(function(event) {
		event.preventDefault();
		if (getAjaxMode() == '') {
			alert('Request method DELETE is not supported by standard forms');
			return;
		}
		restRequest($('#uri').val(), 'DELETE', null, function() {
			var identifier = $('#identity').val();
			$('[data-identifier=' + identifier + ']').remove();
			resetForm();
		});
	});

	resetForm();
});