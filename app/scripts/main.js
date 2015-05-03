	'use strict';
	//$.ajaxSetup({cache:false});
	$.validator.addMethod("lettersonly", function(value, element) {
		return this.optional(element) || /^[áéíóúÁÉÍÓÚA-Za-zñÑçÇ ]+$/i.test(value);
	}, "Por favor, escribe sólo letras.");
	
	$(document).ready(function () {	
		$('#clinicas').load('php/cargar_clinicas.php');
	   	
		var miTabla = $('#mitabla').DataTable({
			"columnDefs": [
				{
					"targets": [ 3, 4 ],
					"orderable": false
				}
			],		
			'processing': true,
			'serverSide': true,
			'ajax': 'php/cargar_doctor_clinicas.php',
			'language': {
				'sProcessing': 'Procesando...',
				'sLengthMenu': 'Mostrar _MENU_ registros',
				'sZeroRecords': 'No se encontraron resultados',
				'sEmptyTable': 'Ningún dato disponible en esta tabla',
				'sInfo': 'Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros',
				'sInfoEmpty': 'Mostrando registros del 0 al 0 de un total de 0 registros',
				'sInfoFiltered': '(filtrado de un total de _MAX_ registros)',
				'sInfoPostFix': '',
				'sSearch': 'Buscar:',
				'sUrl': '',
				'sInfoThousands': ',',
				'sLoadingRecords': 'Cargando...',
				'oPaginate': {
					'sFirst': 'Primero',
					'sLast': 'Último',
					'sNext': 'Siguiente',
					'sPrevious': 'Anterior'
				},
				'oAria': {
					'sSortAscending': ': Activar para ordenar la columna de manera ascendente',
					'sSortDescending': ': Activar para ordenar la columna de manera descendente'
				}
			},
			'columns': [{
				'data': 'doctor',
				'render': function (data) {
					return '<a href="#" data-toggle="modal" data-target="#modalPrincipal" class="editarBtn">' + data + '</a>'
				}
			}, {
				'data': 'numcolegiado'
			}, {
				'data': 'clinicas',
				'render': function (data) {
					var linea = data.replace(/,/g, '</li><li>');
					return '<ul><li>'+linea+'</li></ul>';
				}
			}, {
				'data': 'doctor',
				'render': function (data) {
					return '<a class="btn btn-primary editarBtn" data-toggle="modal" data-target="#modalPrincipal">Editar</a>'
				}
			}, {
				'data': 'doctor',
				'render': function (data) {
					return '<a class="btn btn-warning borrarBtn" data-toggle="modal" data-target="#modalPrincipal">Borrar</a>';
				}
			}]
		});

		// Borrar doctor
		$('#mitabla').on('click', '.borrarBtn', function (e) {
			e.preventDefault();
			var nRow = $(this).parents('tr')[0];
			var aData = miTabla.row(nRow).data();
			var doctor = aData.doctor;
			$('#tituloModal').html("Borrar Doctor");
			$('#formDoctor').hide();
			$('#btnConfirmar').html('<a type="submit" id="btnBorrarDoctor" class="btn btn-primary">Borrar</a>');
			$('#txtConfirmacion').show();
			$('#btnBorrarDoctor').click(function (e) {
				e.preventDefault();
				$('#modalPrincipal').modal('hide');
				$.ajax({
					data: {
						doctor: doctor
					},
					dataType: 'json',
					type: "POST",
					url: "php/borrar_doctor.php",
					error: function(data) {
						miTabla.draw();
						$.growl({
							title: data[0]['mensaje'],
							message: '',
							location: 'tr',
						});
					},
					success: function(data) {
						miTabla.draw();
						$.growl({
							title: data[0]['mensaje'],
							message: '',
							location: 'tr',
						});
					}
				});
			});
		});

		var formulario = $('#datosDoctor').validate({
			rules: {
				nombre: {
					required: true,
					lettersonly: true
				},
				numcolegiado: {
					digits: true
				},
				clinicas: {
					required: true
				}
			}
		});
		
		// Crear nuevo doctor
		$('#btnNuevoDoctor').click(function (e) {
			e.preventDefault();
			formulario.resetForm();
			$('#nombre').val("");
			$('#numcolegiado').val("");
			$('#clinicas option').removeAttr("selected");
			$('#tituloModal').html("Datos del doctor");
			$('#formDoctor').show();
			$('#btnConfirmar').html('<a type="submit" id="btnCrearDoctor" class="btn btn-primary">Guardar</a>');
			$('#txtConfirmacion').hide();
			$('#btnCrearDoctor').click(function (e) {
				e.preventDefault();
				if (formulario.form()) {
					var doctor = $('#nombre').val();
					var numcolegiado = $('#numcolegiado').val();
					var clinicas = $('#clinicas').val();
					$('#modalPrincipal').modal('hide');
					$.ajax({
						data: {
							doctor: doctor,
							numcolegiado: numcolegiado,
							clinicas: clinicas
						},
						dataType: 'json',
						type: "POST",
						url: "php/crear_doctor.php",
						error: function(data) {
							miTabla.draw();
							$.growl({
								title: data[0]['mensaje'],
								message: '',
								location: 'tr',
							});
						},
						success: function(data) {
							miTabla.draw();
							$.growl({
								title: data[0]['mensaje'],
								message: '',
								location: 'tr',
							});
						}
					});
					
				}
			});
		});

		// Editar doctor
		$('#mitabla').on('click', '.editarBtn', function (e) {
			e.preventDefault();
			formulario.resetForm();
			var nRow = $(this).parents('tr')[0];
			var aData = miTabla.row(nRow).data();
			var nombreAntiguo = aData.doctor;
			$('#nombre').val(aData.doctor);
			$('#numcolegiado').val(aData.numcolegiado);
			$('#clinicas').load('php/cargar_clinicas.php', {
				'clinicas': aData.clinicas.split(',')
			});
			$('#tituloModal').html("Datos del doctor");
			$('#formDoctor').show();
			$('#btnConfirmar').html('<a type="submit" id="btnEditarDoctor" class="btn btn-primary">Guardar</a>'); //txt
			$('#txtConfirmacion').hide();
			$('#btnEditarDoctor').click(function (e) {
				e.preventDefault();
				if (formulario.form()) {
					var doctor = $('#nombre').val();
					var numcolegiado = $('#numcolegiado').val();
					var clinicas = $('#clinicas').val();
					$('#modalPrincipal').modal('hide');
					$.ajax({
						data: {
						   nombreAntiguo: nombreAntiguo,
						   doctor: doctor,
						   numcolegiado: numcolegiado,
						   clinicas: clinicas
						},
						dataType: 'json',
						type: "POST",
						url: "php/editar_doctor.php",
						error: function(data) {
							miTabla.draw();
							$.growl({
								title: data[0]['mensaje'],
								message: '',
								location: 'tr',
							});
						},
						success: function(data) {
							miTabla.draw();
							$.growl({
								title: data[0]['mensaje'],
								message: '',
								location: 'tr',
								size: 'medium'
							});
						}
					});
				}
			});
		});
		
	});
